<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserFormController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $now = Carbon::now();
        $forms = Form::where('status', 'publish')
            ->where(function($query) use ($user) {
                $query->where('target_peserta', 'semua')
                      ->orWhereHas('participants', function($q) use ($user) {
                          $q->where('users.id', $user->id);
                      });
            })
            ->orderBy('waktu_mulai', 'desc')
            ->get();

        return view('perawat.ujian_aktif.index', compact('forms', 'now'));
    }

    public function show(Form $form)
    {
        if ($form->target_peserta == 'khusus') {
            if (!$form->participants->contains(auth()->user()->id)) {
                abort(403, 'Anda tidak terdaftar untuk ujian ini.');
            }
        }

        return view('perawat.ujian_aktif.show', compact('form'));
    }
}
