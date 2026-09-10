<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ModulSistem;
use App\Models\RoleModulAkses;
use App\Models\User;
use App\Support\AuditLogger;
use App\Support\EmailAkunAdmin;
use App\Support\KunciLoginAdmin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

/**
 * Akun milik petugas yang sedang masuk.
 *
 * Berbeda dengan modul Pengguna — yang di sana adalah administrator mengelola
 * akun orang lain dan menuntut hak `pengguna`. Endpoint di sini tidak menuntut
 * hak modul apa pun: yang disentuh hanya akun pemiliknya sendiri, dan setiap
 * petugas berhak mengurus akunnya sendiri berapa pun hak modulnya.
 */
class AkunController extends Controller
{
    /**
     * Profil akun sendiri, lengkap dengan role dan matrix hak aksesnya.
     *
     * Hak akses ditampilkan **seluruh modul aktif**, bukan hanya yang boleh
     * dilihat seperti pada `me/navigation`. Halaman ini menjawab pertanyaan
     * "saya sebenarnya boleh apa saja", dan jawabannya tidak lengkap kalau modul
     * yang tertutup untuk role ini justru disembunyikan.
     */
    public function profil(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('api')->user();
        $user->loadMissing(['role', 'struktur']);

        $superAdmin = $user->role?->slug === 'super-admin';

        $hakPerModul = $superAdmin
            ? collect()
            : RoleModulAkses::where('role_id', $user->role_id)->get()->keyBy('modul_id');

        $akses = ModulSistem::where('is_active', true)
            ->orderBy('urutan')
            ->get()
            ->map(function (ModulSistem $modul) use ($hakPerModul, $superAdmin) {
                $hak = $hakPerModul[$modul->id] ?? null;

                return [
                    'modul_id' => $modul->id,
                    'slug' => $modul->slug,
                    'nama' => $modul->nama,
                    'view' => $superAdmin || (bool) ($hak->can_view ?? false),
                    'create' => $superAdmin || (bool) ($hak->can_create ?? false),
                    'edit' => $superAdmin || (bool) ($hak->can_edit ?? false),
                    'delete' => $superAdmin || (bool) ($hak->can_delete ?? false),
                    'approve' => $superAdmin || (bool) ($hak->can_approve ?? false),
                    'export' => $superAdmin || (bool) ($hak->can_export ?? false),
                ];
            })
            ->values();

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'photo_url' => $user->photo_url,
                'is_active' => (bool) $user->is_active,
                'last_login_at' => $user->last_login_at,
                'created_at' => $user->created_at,
                'role' => $user->role === null ? null : [
                    'id' => $user->role->id,
                    'name' => $user->role->name,
                    'slug' => $user->role->slug,
                    'description' => $user->role->description,
                ],
                /*
                 * Kotak yang ditempati akun ini pada bagan struktur organisasi.
                 * Melengkapi role: role menentukan boleh apa, struktur
                 * menentukan siapa dalam bagan.
                 */
                'struktur' => $user->struktur === null ? null : [
                    'id' => $user->struktur->id,
                    'nama' => $user->struktur->nama,
                    'jabatan' => $user->struktur->jabatan,
                ],
                'super_admin' => $superAdmin,
                'akses' => $akses,
            ],
        ]);
    }

    /**
     * Sunting profil sendiri: nama tampilan, nomor telepon, foto.
     *
     * Email dan role sengaja tidak ada di sini. Email adalah identitas masuk
     * sekaligus alamat pemberitahuan keamanan, dan role menentukan hak akses —
     * keduanya urusan administrator lewat modul Pengguna, bukan sesuatu yang
     * bisa digeser sendiri oleh pemiliknya.
     */
    public function perbaruiProfil(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('api')->user();

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'photo_url' => ['sometimes', 'nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama tidak boleh kosong.',
        ]);

        $sebelum = [
            'name' => $user->name,
            'phone' => $user->phone,
            'photo_url' => $user->photo_url,
        ];

        $user->fill($data);
        $user->save();

        AuditLogger::record(
            $user->id,
            'ubah_profil_sendiri',
            User::class,
            $user->id,
            $sebelum,
            $user->only(['name', 'phone', 'photo_url'])
        );

        return response()->json(['data' => $user->only([
            'id', 'name', 'email', 'phone', 'photo_url',
        ])]);
    }

    /**
     * Riwayat aktivitas akun sendiri, dibaca dari `audit_log`.
     *
     * Tidak digantung hak modul Audit Log: yang ditampilkan hanya baris dengan
     * `user_id` milik pemanggil. Nilai lama/barunya sengaja tidak ikut — isinya
     * bisa memuat data modul yang rolenya sendiri tidak berhak melihat.
     */
    public function aktivitas(Request $request): JsonResponse
    {
        $userId = (int) Auth::guard('api')->id();

        $perPage = min(max((int) $request->query('per_page', 15), 1), 100);
        $page = max((int) $request->query('page', 1), 1);

        $paginator = AuditLog::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', $page);

        $baris = collect($paginator->items())->map(fn (AuditLog $log) => [
            'id' => $log->id,
            'action' => $log->action,
            // Nama kelasnya saja: panel tidak perlu tahu namespace-nya, dan FQCN
            // hanya memperpanjang kolom tanpa menambah keterangan.
            'model' => $log->model_type === null ? null : class_basename($log->model_type),
            'model_id' => $log->model_id,
            'ip_address' => $log->ip_address,
            'created_at' => $log->created_at,
        ]);

        return response()->json([
            'data' => $baris,
            'meta' => [
                'total' => $paginator->total(),
                'page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    /**
     * Ganti password sendiri.
     *
     * Password lama tetap dituntut walau tokennya sudah sah. Token bisa saja
     * terbawa perangkat yang ditinggal terbuka; menuntut password lama membuat
     * layar yang lupa dikunci tidak cukup untuk mengambil alih akunnya.
     */
    public function ubahPassword(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('api')->user();

        $data = $request->validate([
            'password_lama' => ['required', 'string', 'max:255'],
            /*
             * Syaratnya disamakan dengan pembuatan akun di modul Pengguna.
             * Kalau lebih longgar, halaman ini berubah menjadi jalan memutar
             * untuk menurunkan password yang sudah dipasang administrator.
             */
            'password' => [
                'required',
                'confirmed',
                PasswordRule::min(12)->letters()->mixedCase()->numbers()->symbols(),
            ],
        ], [
            'password_lama.required' => 'Password lama wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Ulangan password baru tidak sama.',
        ]);

        if (!Hash::check($data['password_lama'], (string) $user->password)) {
            throw ValidationException::withMessages([
                'password_lama' => 'Password lama tidak cocok.',
            ]);
        }

        // Mengganti password dengan password yang sama tidak mengubah apa pun,
        // tetapi tetap mengirim email "password Anda diubah" — pemberitahuan
        // yang menakuti tanpa ada yang benar-benar terjadi.
        if (Hash::check($data['password'], (string) $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Password baru harus berbeda dari password lama.',
            ]);
        }

        /*
         * Disimpan diam-diam, sama seperti jalur atur ulang password: kolom
         * "Diubah oleh/Diubah" pada modul Pengguna dipakai untuk menandai
         * penyuntingan data akun (nama, role, status), bukan pergantian
         * kredensial. Jejaknya tetap lengkap — audit_log mencatat pelakunya,
         * dan pemiliknya menerima email pemberitahuan.
         */
        $user->timestamps = false;
        $user->forceFill([
            'password' => $data['password'], // cast 'hashed' di model yang mengenkripsi
            'remember_token' => Str::random(60),
        ])->saveQuietly();
        $user->timestamps = true;

        // Password baru menutup keadaan yang membuat kuncinya dipasang; sama
        // alasannya dengan pada jalur atur ulang password.
        KunciLoginAdmin::bersihkan($request, (string) $user->email);

        AuditLogger::record($user->id, 'ubah_password', User::class, $user->id);

        EmailAkunAdmin::passwordDiubah($user, 'oleh Anda sendiri dari halaman Akun Saya di panel');

        /*
         * Token yang sedang dipakai tetap berlaku, jadi petugas tidak terlempar
         * keluar di tengah pekerjaan. Token yang terlanjur ada di perangkat lain
         * pun masih berlaku sampai masa berlakunya habis — JWT tidak menyimpan
         * kaitan ke password. Bila akunnya diduga sudah berpindah tangan,
         * administrator perlu menonaktifkan akunnya di modul Pengguna.
         */
        return response()->json(['message' => 'Password berhasil diubah.']);
    }
}
