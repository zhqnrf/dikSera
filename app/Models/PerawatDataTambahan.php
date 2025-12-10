<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerawatDataTambahan extends Model
{
    use HasFactory;

    protected $table = 'perawat_data_tambahans';
    protected $fillable = [
        'user_id',
        'jenis',
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
