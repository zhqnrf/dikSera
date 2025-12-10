<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerawatLisensi extends Model
{
    use HasFactory;

    protected $table = 'perawat_lisensis';
    protected $fillable = [
        'user_id',
        'nomor',
        'nama',
        'lembaga',
        'tgl_terbit',
        'tgl_expired',
        'file_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
