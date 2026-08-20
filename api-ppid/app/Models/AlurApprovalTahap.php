<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Satu jenjang pada alur persetujuan.
 *
 * `role_id` menentukan siapa yang boleh memutuskan, `struktur_id` menautkannya
 * ke kotak pada bagan struktur organisasi supaya jenjang yang tampil di panel
 * memakai nama jabatan yang sama dengan yang dilihat publik.
 */
class AlurApprovalTahap extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'alur_approval_tahap';

    protected $fillable = [
        'alur_id',
        'urutan',
        'nama',
        'role_id',
        'struktur_id',
        'sla_hari',
        'boleh_tolak',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'sla_hari' => 'integer',
        'boleh_tolak' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function alur(): BelongsTo
    {
        return $this->belongsTo(AlurApproval::class, 'alur_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function struktur(): BelongsTo
    {
        return $this->belongsTo(StrukturOrganisasi::class, 'struktur_id');
    }
}
