<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Set / reset kata sandi user admin dari CLI.
 *
 *   php artisan ppid:set-password admin@foodstation.co.id
 *
 * Kata sandi diminta lewat prompt tersembunyi supaya tidak tersimpan
 * di riwayat shell. Untuk otomasi, boleh dioper sebagai argumen kedua.
 */
class SetUserPassword extends Command
{
    protected $signature = 'ppid:set-password {email} {password?}';

    protected $description = 'Set ulang kata sandi user admin';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (!$user) {
            $this->error('User tidak ditemukan: '.$this->argument('email'));

            return self::FAILURE;
        }

        $password = $this->argument('password') ?: $this->secret('Kata sandi baru');

        if (strlen((string) $password) < 12) {
            $this->error('Kata sandi minimal 12 karakter.');

            return self::FAILURE;
        }

        $user->forceFill(['password' => Hash::make($password)])->save();

        $this->info("Kata sandi untuk {$user->email} berhasil diperbarui.");

        return self::SUCCESS;
    }
}
