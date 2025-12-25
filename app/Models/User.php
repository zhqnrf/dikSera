<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\PerawatProfile;
use App\Models\PerawatSip;
use App\Models\PerawatStr;
use App\Models\PerawatLisensi;
use App\Models\PerawatDataTambahan;
use App\Models\ExamResult;
use App\Models\PenanggungJawabUjian;
use App\Models\PerawatPendidikan;
use App\Models\PerawatPekerjaan;
use App\Models\PerawatPelatihan;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'telegram_chat_id',
        'telegram_verification_code',
        'telegram_verification_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function penanggungJawab()
    {
        return $this->hasOne(PenanggungJawabUjian::class);
    }

    public function Perawatprofile()
    {
        return $this->hasOne(PerawatProfile::class, 'user_id');
    }
    public function profile()
    {
        return $this->hasOne(PerawatProfile::class, 'user_id');
    }
    public function lisensis()
    {
        return $this->hasMany(PerawatLisensi::class);
    }
    public function sips()
    {
        return $this->hasMany(PerawatSip::class);
    }

    public function strs()
    {
        return $this->hasMany(PerawatStr::class);
    }
    public function perawatPekerjaans()
    {
        return $this->hasMany(PerawatPekerjaan::class);
    }

    public function perawatLisensis()
    {
        return $this->hasMany(PerawatLisensi::class);
    }

    public function dataTambahans()
    {
        return $this->hasMany(PerawatDataTambahan::class);
    }

    public function getDokumenWarningAttribute()
    {
        $warnings = [];
        $threshold = now()->addMonth();
        // 1. Cek STR
        $str = $this->strs()->latest()->first();
        if ($str && $str->tgl_expired <= $threshold) {
            $warnings[] = 'STR';
        }
        // 2. Cek SIP
        $sip = $this->sips()->latest()->first();
        if ($sip && $sip->tgl_expired <= $threshold) {
            $warnings[] = 'SIP';
        }
        // 3. Cek Lisensi
        $lisensi = $this->lisensis()->latest()->first();
        if ($lisensi && $lisensi->tgl_expired <= $threshold) {
            $warnings[] = 'Lisensi';
        }
        // 4. Cek Dokumen Tambahan
        $dokumenLain = $this->dataTambahans()
            ->where('tgl_expired', '<=', $threshold)
            ->exists();
        if ($dokumenLain) {
            $warnings[] = 'Dok. Tambahan';
        }

        return $warnings;
    }
    public function examResult()
    {
        return $this->hasOne(ExamResult::class, 'user_id')->latest();
    }

    public function pendidikanTerakhir()
    {
        return $this->hasOne(PerawatPendidikan::class, 'user_id')
            ->orderBy('tahun_lulus', 'desc');
    }
}
