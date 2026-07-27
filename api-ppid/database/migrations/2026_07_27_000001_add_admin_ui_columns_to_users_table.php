<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom tambahan yang dibutuhkan panel admin (be-ppid / Fuse React):
 * foto profil, shortcut menu, dan preferensi tampilan per user.
 * Tabel `users` sendiri sudah dibuat lewat DDL awal, jadi ini hanya menambah kolom.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'photo_url')) {
                $table->string('photo_url', 500)->nullable();
            }

            if (!Schema::hasColumn('users', 'shortcuts')) {
                $table->jsonb('shortcuts')->nullable();
            }

            if (!Schema::hasColumn('users', 'settings')) {
                $table->jsonb('settings')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['photo_url', 'shortcuts', 'settings']);
        });
    }
};
