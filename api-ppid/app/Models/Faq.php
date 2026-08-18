<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'faq';

    protected $fillable = [
        'pertanyaan',
        'pertanyaan_en',
        'jawaban',
        'jawaban_en',
        'kategori',
        'kategori_en',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'is_active' => 'boolean',
    ];
}
