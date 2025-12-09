<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerawatLisensi extends Model
{
    use HasFactory;

    protected $table = 'lisensi_perawats';

    protected $fillable = [
        'perawat_profile_id', 
        'jenis',
        'nomor',
        'tgl_terbit',
        'tgl_expired',
        'file_path',
        'status',
    ];

    protected $casts = [
        'tgl_terbit' => 'date',
        'tgl_expired' => 'date',
    ];

    // Relasi balik ke Profile (Opsional, tapi berguna)
    public function profile()
    {
        return $this->belongsTo(PerawatProfile::class, 'perawat_profile_id');
    }
}
