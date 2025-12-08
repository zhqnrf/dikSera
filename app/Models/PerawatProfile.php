<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerawatProfile extends Model
{
    protected $table = 'perawat_profiles';

    protected $fillable = [
        'user_id',

        // Identitas Utama
        'nik',
        'nip',
        'nirp',              
        'nama_lengkap',

        // Biodata
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'aliran_kepercayaan',
        'status_perkawinan',

        // Jabatan & Pangkat
        'jabatan',
        'pangkat',
        'golongan',

        // Kontak
        'alamat',
        'kota',
        'no_hp',

        // Fisik
        'golongan_darah',
        'tinggi_badan',
        'berat_badan',
        'rambut',
        'bentuk_muka',
        'warna_kulit',
        'ciri_khas',
        'cacat_tubuh',
        'hobby',
        'foto_3x4',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
