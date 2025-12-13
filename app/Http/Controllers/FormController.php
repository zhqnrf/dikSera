<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\User;
use App\Models\PenanggungJawabUjian;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormController extends Controller
{
    public function index()
    {
        $forms = Form::with('penanggungJawab')->latest()->get();
        return view('admin.form.index', compact('forms'));
    }

    public function create()
    {
        $pjs = PenanggungJawabUjian::all();

        $users = User::where('role', 'perawat')->get();
        $users = $users->sortByDesc(function ($user) {
            return count($user->dokumen_warning) > 0;
        });

        // Kirim $pjs ke view
        return view('admin.form.create', compact('users', 'pjs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'penanggung_jawab_id' => 'required|exists:penanggung_jawab_ujians,id', // Validasi PJ
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
            'penanggung_jawab_id' => $request->penanggung_jawab_id, // Simpan PJ
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

    public function edit(Form $form)
    {
        $pjs = PenanggungJawabUjian::all();

        $users = User::where('role', 'perawat')->get();
        $users = $users->sortByDesc(function ($user) {
            return count($user->dokumen_warning) > 0;
        });

        $form->load('participants');
        $selectedParticipants = $form->participants->pluck('id')->toArray();

        // Kirim $pjs ke view edit
        return view('admin.form.edit', compact('form', 'users', 'selectedParticipants', 'pjs'));
    }

    public function update(Request $request, Form $form)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'penanggung_jawab_id' => 'required|exists:penanggung_jawab_ujians,id', // Validasi PJ
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'target_peserta' => 'required',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
        ]);

        $form->update([
            'judul' => $request->judul,
            'slug' => Str::slug($request->judul) . '-' . Str::random(5),
            'deskripsi' => $request->deskripsi,
            'penanggung_jawab_id' => $request->penanggung_jawab_id, // Update PJ
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'target_peserta' => $request->target_peserta,
        ]);

        if ($request->target_peserta == 'khusus') {
            $form->participants()->sync($request->participants ?? []);
        } else {
            $form->participants()->detach();
        }

        return redirect()->route('admin.form.index')->with('success', 'Form berhasil diperbarui!');
    }
    public function destroy(Form $form)
    {
        $form->delete();
        return back()->with('success', 'Form berhasil dihapus!');
    }
}
