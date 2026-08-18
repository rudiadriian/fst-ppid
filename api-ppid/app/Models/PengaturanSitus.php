<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengaturanSitus extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'pengaturan_situs';

    protected $fillable = [
        'key',
        'value',
        'group_name',
    ];
}
