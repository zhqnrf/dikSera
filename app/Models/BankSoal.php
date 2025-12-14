<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankSoal extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Casting JSON agar otomatis jadi Array saat dipanggil
    protected $casts = [
        'opsi_jawaban' => 'array',
    ];

    public function forms()
    {
        return $this->belongsToMany(Form::class, 'form_questions')
            ->withPivot('bobot')
            ->withTimestamps();
    }
}
