<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Notifikasi lonceng Portal Pemohon.
 *
 * Barisnya ditulis api-ppid setiap petugas memberi umpan balik (status
 * pengajuan berpindah, berkas tanggapan dilampirkan, hasil verifikasi data
 * diri diputuskan). Portal hanya membacanya dan menandainya sudah dibaca —
 * kebalikan dari `Notifikasi`, yang hanya ditulis portal untuk panel admin.
 */
class NotifikasiPemohon extends Model
{
    protected $table = 'notifikasi_pemohon';

    /** Tabelnya tidak punya kolom `updated_at`. */
    public const UPDATED_AT = null;

    /**
     * Yang ditampilkan lonceng dibatasi agar akun lama tidak menarik ratusan
     * baris tiap kali daftarnya dibuka.
     */
    public const BATAS_TAMPIL = 20;

    protected $fillable = [
        'pemohon_id',
        'type',
        'message',
        'is_read',
        'data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'data' => 'array',
    ];

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(Pemohon::class, 'pemohon_id');
    }

    public function scopeMilik(Builder $query, int $pemohonId): Builder
    {
        return $query->where('pemohon_id', $pemohonId);
    }

    /**
     * Bentuk ringkas untuk lonceng.
     *
     * Tautannya disimpan sebagai path relatif oleh api-ppid; apa pun selain
     * path yang diawali satu garis miring dibuang supaya notifikasi tidak bisa
     * dipakai melempar pemohon ke domain lain.
     *
     * @return array<string, mixed>
     */
    public function untukLonceng(): array
    {
        $data = $this->data ?? [];
        $tautan = (string) ($data['link'] ?? '');
        $aman = (bool) preg_match('#^/[^/\\\\]#', $tautan);

        return [
            'id' => $this->id,
            'tipe' => (string) $this->type,
            'judul' => (string) ($data['title'] ?? __('Pemberitahuan')),
            'pesan' => (string) $this->message,
            'tautan' => $aman ? url($tautan) : null,
            'varian' => ($data['variant'] ?? null) === 'warning' ? 'warning' : 'primary',
            'dibaca' => (bool) $this->is_read,
            'waktu' => $this->created_at
                ?->copy()
                ->timezone(config('ppid.zona_waktu'))
                ->translatedFormat('d M Y H:i'),
        ];
    }
}
