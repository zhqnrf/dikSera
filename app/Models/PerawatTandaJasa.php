<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerawatTandaJasa extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'perawat_tandajasa';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nama_penghargaan',
        'instansi_pemberi', // <-- TAMBAH
        'tahun',
        'nomor_sk',         // <-- TAMBAH
        'tanggal_sk',       // <-- TAMBAH
        'dokumen_path',
        // Unused 
        'keterangan',
    ];

    /**
     * Get the user that owns the award record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}