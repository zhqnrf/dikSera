<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Agar otomatis jadi object Carbon (bisa diformat tanggalnya)
    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    public function participants()
    {
        return $this->belongsToMany(User::class, 'form_user');
    }
}
