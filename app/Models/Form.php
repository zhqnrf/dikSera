<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    public function participants()
    {
        return $this->belongsToMany(User::class, 'form_user');
    }

    public function penanggungJawab()
    {
        return $this->belongsTo(PenanggungJawabUjian::class, 'penanggung_jawab_id');
    }
}
