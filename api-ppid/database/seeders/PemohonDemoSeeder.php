<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Akun pengunjung contoh untuk mencoba login di situs publik (fe-ppid).
 *
 * Idempoten: dicocokkan lewat `email`, jadi aman dijalankan ulang.
 * Akun ini hanya berlaku di situs publik (guard `pemohon`) — tidak bisa
 * dipakai masuk ke panel admin be-ppid yang memakai tabel `users`.
 *
 *   php artisan db:seed --class=PemohonDemoSeeder
 */
class PemohonDemoSeeder extends Seeder
{
    public const EMAIL = 'pemohon.demo@foodstation.co.id';
    public const PASSWORD = 'Pemohon@2026';

    public function run(): void
    {
        $sekarang = now();

        DB::table('pemohon')->updateOrInsert(
            ['email' => self::EMAIL],
            [
                'nama' => 'Pemohon Demo',
                'nik' => '3175010101900001',
                'no_hp' => '081200000001',
                'alamat' => 'Jl. Pisangan Lama Selatan No. 1, Jakarta Timur',
                'pekerjaan' => 'Karyawan Swasta',
                'jenis_pemohon' => 'pribadi',
                'password' => Hash::make(self::PASSWORD),
                'email_verified_at' => $sekarang,
                'updated_at' => $sekarang,
                'created_at' => $sekarang,
                'deleted_at' => null,
            ]
        );

        $this->command?->info('Akun demo: '.self::EMAIL.' / '.self::PASSWORD);
    }
}
