<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormController extends Controller
{
    public function index()
    {
        $forms = Form::latest()->get();
        return view('admin.form.index', compact('forms'));
    }

    public function create()
    {
        $users = User::where('role', 'perawat')->get();
        $users = $users->sortByDesc(function ($user) {
            return count($user->dokumen_warning) > 0;
        });

        return view('admin.form.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'target_peserta' => 'required',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
        ]);

        $form = Form::create([
            'judul' => $request->judul,
            'slug' => Str::slug($request->judul) . '-' . Str::random(5),
            'deskripsi' => $request->deskripsi,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'target_peserta' => $request->target_peserta,
            'status' => 'draft',
        ]);

        if ($request->target_peserta == 'khusus' && $request->has('participants')) {
            $form->participants()->attach($request->participants);
        }

        return redirect()->route('admin.form.index')->with('success', 'Form berhasil dibuat!');
    }

    public function updateStatus(Request $request, Form $form)
    {
        $request->validate([
            'status' => 'required|in:draft,publish,closed'
        ]);

        $form->update([
            'status' => $request->status
        ]);

        return back()->with('success', "Status berhasil diubah menjadi " . ucfirst($request->status));
    }

    // Nanti tambahkan edit, update, destroy di sini
}
