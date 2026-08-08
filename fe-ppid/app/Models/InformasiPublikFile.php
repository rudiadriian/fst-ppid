<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformasiPublikFile extends Model
{
    protected $table = 'informasi_publik_files';

    const UPDATED_AT = null;

    protected $casts = [
        'ukuran_file' => 'integer',
        'urutan'      => 'integer',
    ];
}
