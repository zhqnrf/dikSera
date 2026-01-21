<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanSertifikat extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];

    public function jadwalWawancara()
    {
        return $this->hasOne(JadwalWawancara::class, 'pengajuan_sertifikat_id'); 
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function lisensiLama()
    {
        return $this->belongsTo(PerawatLisensi::class, 'lisensi_lama_id');
    }

    public function penanggungJawab()
    {
        return $this->belongsTo(PenanggungJawabUjian::class, 'penanggung_jawab_id');
    }

    /**
     * LOGIC BERSIH-BERSIH NILAI & JADWAL
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($pengajuan) {
            // 1. Hapus Jadwal Wawancara
            if ($pengajuan->jadwalWawancara) {
                $pengajuan->jadwalWawancara->delete();
            }

            // 2. HAPUS HASIL UJIAN (RESET NILAI USER)
            if ($pengajuan->user && $pengajuan->user->examResult) {
                $pengajuan->user->examResult->delete();
            }
        });
    }
}