<?php

namespace App\Http\Controllers;

use App\Models\BankSoal;
use Illuminate\Http\Request;

class BankSoalController extends Controller
{
    public function index()
    {
        $soals = BankSoal::latest()->get();

        return view('admin.bank_soal.index', compact('soals'));
    }

    public function create()
    {
        return view('admin.bank_soal.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'pertanyaan'     => 'required|string',
            'kategori'       => 'required|string',
            'opsi.a'         => 'required|string',
            'opsi.b'         => 'required|string',
            'opsi.c'         => 'required|string',
            'opsi.d'         => 'required|string',
            'opsi.e'         => 'required|string',
            'kunci_jawaban'  => 'required|in:a,b,c,d,e',
        ]);

        BankSoal::create([
            'pertanyaan'   => $request->pertanyaan,
            'kategori'     => $request->kategori,
            'opsi_jawaban' => $request->opsi,
            'kunci_jawaban' => $request->kunci_jawaban,
        ]);

        return redirect()
            ->route('admin.bank-soal.index')
            ->with('success', 'Soal berhasil ditambahkan');
    }

    public function edit($id)
    {
        $soal = BankSoal::findOrFail($id);

        return view('admin.bank_soal.edit', compact('soal'));
    }

    public function update(Request $request, $id)
    {
        $soal = BankSoal::findOrFail($id);

        $request->validate([
            'pertanyaan'     => 'required|string',
            'kategori'       => 'required|string',
            'opsi.a'         => 'required|string',
            'opsi.b'         => 'required|string',
            'opsi.c'         => 'required|string',
            'opsi.d'         => 'required|string',
            'opsi.e'         => 'required|string',
            'kunci_jawaban'  => 'required|in:a,b,c,d,e',
        ]);

        $soal->update([
            'pertanyaan'   => $request->pertanyaan,
            'kategori'     => $request->kategori,
            'opsi_jawaban' => $request->opsi,
            'kunci_jawaban' => $request->kunci_jawaban,
        ]);

        return redirect()
            ->route('admin.bank-soal.index')
            ->with('success', 'Soal berhasil diperbarui');
    }

    public function destroy($id)
    {
        $soal = BankSoal::findOrFail($id);
        $soal->delete();

        return back()->with('success', 'Soal berhasil dihapus');
    }
}
