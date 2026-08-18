<?php

namespace App\Http\Requests\Akun;

use App\Models\Pemohon;
use App\Rules\CaptchaBenar;
use App\Support\KunciLoginPemohon;
use App\Support\PerisaiFormulir;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Permintaan login akun pengunjung.
 *
 * Satu isian `identitas` menerima email **atau** nomor telepon: pemohon lebih
 * hafal nomor WhatsApp-nya daripada email yang dipakai mendaftar. Bentuknya
 * ditebak dari isinya, lalu dicocokkan ke kolom yang sesuai.
 *
 * Tiga rem berjalan bersamaan supaya penebakan password tidak murah:
 *   - captcha, menahan skrip yang mengirim langsung ke endpoint;
 *   - batas per kombinasi identitas + IP, menahan gempuran ke satu akun;
 *   - batas per IP saja, menahan gempuran ke banyak akun dari satu tempat.
 */
class LoginPemohonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'identitas' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
            // Wajibnya ikut sakelar konfigurasi — lihat catatan yang sama di
            // RegisterController.
            'captcha' => [
                config('ppid.akun.captcha_aktif') ? 'required' : 'nullable',
                'string',
                new CaptchaBenar(),
            ],
        ];
    }

    public function attributes(): array
    {
        return ['identitas' => __('Email atau nomor telepon')];
    }

    /** @throws \Illuminate\Validation\ValidationException */
    public function authenticate(): void
    {
        PerisaiFormulir::periksa($this);

        $identitas = trim((string) $this->string('identitas'));

        // Kunci bertingkat diperiksa lebih dulu: percuma menghitung rem
        // per-menit bila kombinasi ini memang sedang menunggu berjam-jam.
        KunciLoginPemohon::pastikanTidakTerkunci($this, $identitas);

        $this->ensureIsNotRateLimited();

        if (!Auth::guard('pemohon')->attempt($this->kredensial())) {
            RateLimiter::hit($this->throttleKey());
            RateLimiter::hit($this->throttleKeyIp());

            // Melempar sendiri bila kegagalan ini memasang kunci baru, supaya
            // pesannya menyebut lama tunggunya, bukan sekadar "tidak cocok".
            KunciLoginPemohon::catatGagal($this, $identitas);

            // Pesannya tidak membedakan "akun tidak ada" dan "password salah"
            // supaya halaman ini tidak bisa dipakai menebak siapa yang terdaftar.
            throw ValidationException::withMessages([
                'identitas' => __('Email/nomor telepon atau password tidak cocok.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        RateLimiter::clear($this->throttleKeyIp());
        KunciLoginPemohon::bersihkan($this, $identitas);
    }

    /**
     * Kredensial yang dikirim ke guard, selalu dalam bentuk email + password.
     *
     * Login lewat nomor telepon diterjemahkan dulu menjadi email pemiliknya,
     * bukan diserahkan ke guard sebagai `['no_hp' => …]`. Alasannya nomor tidak
     * dijamin unik di tabel `pemohon` — baris lama bisa saja diinput petugas
     * dengan nomor yang sama — sehingga pencocokan langsung bisa mendarat di
     * baris yang salah. Di sini hanya baris yang benar-benar punya akun
     * (berpassword) yang dipertimbangkan, dan yang terlama dipakai.
     *
     * Nilai apa pun masuk sebagai parameter terikat, tidak pernah disambung ke
     * SQL, jadi isian aneh tidak bisa dipakai menyuntik query.
     */
    private function kredensial(): array
    {
        $identitas = trim((string) $this->string('identitas'));
        $password = (string) $this->string('password');

        if (str_contains($identitas, '@')) {
            return ['email' => Str::lower($identitas), 'password' => $password];
        }

        return ['email' => $this->emailPemilikNomor($identitas), 'password' => $password];
    }

    /**
     * Email pemilik sebuah nomor telepon; string kosong bila tidak ketemu.
     *
     * String kosong sengaja tetap diteruskan ke guard supaya percobaannya gagal
     * lewat jalur yang sama — waktu tanggapannya jadi mirip dengan password
     * salah, dan halaman ini tidak bisa dipakai menebak nomor mana yang
     * terdaftar.
     */
    private function emailPemilikNomor(string $nomor): string
    {
        $angka = preg_replace('/\D+/', '', $nomor) ?? '';

        if (strlen($angka) < 8) {
            return '';
        }

        // Pemohon menulis nomornya bermacam-macam (`0812…`, `+62 812-…`), dan
        // yang tersimpan pun demikian. Kedua sisi diringkas ke angka saja, lalu
        // varian `0…` dan `62…` sama-sama dicoba.
        $varian = [$angka];

        if (str_starts_with($angka, '62')) {
            $varian[] = '0'.substr($angka, 2);
        } elseif (str_starts_with($angka, '0')) {
            $varian[] = '62'.substr($angka, 1);
        }

        return (string) Pemohon::query()
            ->whereNotNull('password')
            ->whereIn(DB::raw("regexp_replace(coalesce(no_hp, ''), '\\D', '', 'g')"), $varian)
            ->orderBy('id')
            ->value('email');
    }

    /** @throws \Illuminate\Validation\ValidationException */
    public function ensureIsNotRateLimited(): void
    {
        $batas = (int) config('ppid.akun.batas_percobaan_login', 5);
        $batasIp = (int) config('ppid.akun.batas_percobaan_login_ip', 20);

        $terkunci = RateLimiter::tooManyAttempts($this->throttleKey(), $batas)
            || RateLimiter::tooManyAttempts($this->throttleKeyIp(), $batasIp);

        if (!$terkunci) {
            return;
        }

        event(new Lockout($this));

        $detik = max(
            RateLimiter::availableIn($this->throttleKey()),
            RateLimiter::availableIn($this->throttleKeyIp())
        );

        throw ValidationException::withMessages([
            'identitas' => __('Terlalu banyak percobaan masuk. Coba lagi dalam :detik detik.', [
                'detik' => $detik,
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return 'akun-pemohon|'.Str::transliterate(Str::lower($this->string('identitas')).'|'.$this->ip());
    }

    public function throttleKeyIp(): string
    {
        return 'akun-pemohon-ip|'.$this->ip();
    }
}
