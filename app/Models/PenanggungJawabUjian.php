<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenanggungJawabUjian extends Model
{
    use HasFactory;

    protected $table = 'penanggung_jawab_ujians';

    protected $fillable = [
        'nama',
        'no_hp',
        'jabatan',
    ];
}
