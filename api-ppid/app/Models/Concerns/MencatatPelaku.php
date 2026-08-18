<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Pencatat pelaku perubahan pada satu baris data.
 *
 * Kolom `created_by`, `updated_by`, dan `deleted_by` diisi otomatis dari
 * pengguna panel yang sedang login, jadi tidak ada controller yang perlu
 * mengingatnya dan tidak ada jalur simpan yang bisa melewatkannya. Ketiganya
 * sengaja tidak `fillable`: pelaku ditentukan token, bukan isian klien.
 *
 * Ini melengkapi tabel `audit_log` — log berisi riwayat lengkap tiap aksi,
 * sedangkan kolom-kolom ini adalah keadaan terakhir yang bisa ditampilkan dan
 * diurutkan langsung di daftar modul.
 *
 * **Kolom "Diubah" hanya terisi oleh perubahan isi yang sungguhan.** Baris yang
 * baru dibuat dan belum pernah disunting memiliki `updated_at`/`updated_by`
 * kosong, bukan tersalin dari waktu pembuatan; penghapusan pun tidak
 * mengisinya, karena menghapus bukan mengubah isi. Tanpa aturan ini kolom
 * "Diubah" selalu terisi dan kehilangan artinya.
 */
trait MencatatPelaku
{
    public static function bootMencatatPelaku(): void
    {
        static::creating(function (Model $model) {
            // Laravel menyamakan `updated_at` dengan `created_at` saat insert.
            // Diset null lebih dulu supaya kolomnya dianggap "sudah diisi
            // sendiri" dan tidak ikut distempel — baris baru berarti belum
            // pernah diubah.
            $kolomUbah = $model->getUpdatedAtColumn();

            if ($kolomUbah !== null) {
                $model->setAttribute($kolomUbah, null);
            }

            $model->setAttribute('updated_by', null);

            $pelaku = self::idPelaku();

            if ($pelaku !== null && $model->getAttribute('created_by') === null) {
                $model->setAttribute('created_by', $pelaku);
            }
        });

        static::updating(function (Model $model) {
            $pelaku = self::idPelaku();

            if ($pelaku !== null) {
                $model->setAttribute('updated_by', $pelaku);
            }
        });

        static::deleting(function (Model $model) {
            if (!$model->pakaiSoftDelete() || $model->isForceDeleting()) {
                return;
            }

            // Menghapus bukan mengubah isi: cap waktu "Diubah" dimatikan supaya
            // `runSoftDelete()` tidak ikut menstempel `updated_at`.
            $model->timestamps = false;
        });

        static::deleted(function (Model $model) {
            if (!$model->pakaiSoftDelete() || $model->isForceDeleting()) {
                return;
            }

            $model->timestamps = true;

            $pelaku = self::idPelaku();

            // Hard delete tidak menyisakan baris untuk dicatat; jejaknya ada di
            // `audit_log`. Yang bisa diisi hanya penghapusan lunak.
            if ($pelaku === null) {
                return;
            }

            // Query builder mentah: `Eloquent\Builder::update()` akan menambah
            // `updated_at` sendiri, dan itu justru yang dihindari di sini.
            DB::table($model->getTable())
                ->where($model->getKeyName(), $model->getKey())
                ->update(['deleted_by' => $pelaku]);

            $model->setAttribute('deleted_by', $pelaku);
            $model->syncOriginalAttribute('deleted_by');
        });

        static::restoring(function (Model $model) {
            $model->setAttribute('deleted_by', null);
        });
    }

    /**
     * ID pengguna panel yang sedang login; null bila aksi datang dari luar
     * panel (situs publik, seeder, perintah artisan).
     */
    protected static function idPelaku(): ?int
    {
        return Auth::guard('api')->id() ?? Auth::id();
    }

    public function pakaiSoftDelete(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive(static::class), true);
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pengubah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function penghapus(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
