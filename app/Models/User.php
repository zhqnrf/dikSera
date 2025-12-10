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

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'telegram_chat_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

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

    public function dataTambahans()
    {
        return $this->hasMany(PerawatDataTambahan::class);
    }
}
