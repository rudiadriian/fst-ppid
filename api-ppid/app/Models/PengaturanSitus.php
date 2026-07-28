<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanSitus extends Model
{
    protected $table = 'pengaturan_situs';

    public $timestamps = false;

    protected $fillable = [
        'key',
        'value',
        'group_name',
    ];
}
