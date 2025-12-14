<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_correct' => 'boolean', // Agar outputnya true/false, bukan 1/0
        'nilai_diperoleh' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function question()
    {
        // Kita namakan 'question' biar enak dibaca, tapi mengacu ke 'bank_soal_id'
        return $this->belongsTo(BankSoal::class, 'bank_soal_id');
    }
}
