<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maklumat extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'maklumat';

    protected $fillable = [
        'judul',
        'judul_en',
        'ringkasan',
        'ringkasan_en',
        'file_dokumen',
        'tanggal_terbit',
        'status',
        'published_by',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
    ];

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}
