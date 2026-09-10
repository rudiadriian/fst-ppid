<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
