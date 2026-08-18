<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Baris baru berarti belum pernah diubah.
 *
 * Laravel menyamakan `updated_at` dengan `created_at` saat insert, sehingga
 * kolom "Diubah pada" di panel be-ppid terisi pada baris yang belum pernah
 * disunting sama sekali. Kolomnya diset null lebih dulu supaya dianggap "sudah
 * diisi sendiri" dan tidak ikut distempel.
 *
 * Pasangannya di api-ppid adalah trait `MencatatPelaku`, yang selain ini juga
 * mencatat pelaku perubahan. Di sini pelakunya tidak dicatat: baris ini dibuat
 * pengunjung situs publik, bukan pengguna panel.
 */
trait TanpaCapUbahSaatDibuat
{
    public static function bootTanpaCapUbahSaatDibuat(): void
    {
        static::creating(function (Model $model) {
            $kolom = $model->getUpdatedAtColumn();

            if ($kolom !== null) {
                $model->setAttribute($kolom, null);
            }
        });
    }
}
