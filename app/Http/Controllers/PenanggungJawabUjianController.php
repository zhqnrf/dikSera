<?php

namespace App\Http\Controllers;

use App\Models\PenanggungJawabUjian;
use Illuminate\Http\Request;

class PenanggungJawabUjianController extends Controller
{
    public function index()
    {
        $data = PenanggungJawabUjian::latest()->get();
        return view('admin.penanggung_jawab_ujian.index', compact('data'));
    }

    public function create()
    {
        return view('admin.penanggung_jawab_ujian.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'jabatan' => 'required|string|max:255',
        ]);

        PenanggungJawabUjian::create($request->all());

        return redirect()->route('admin.penanggung-jawab.index')
            ->with('success', 'Data penanggung jawab berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $item = PenanggungJawabUjian::findOrFail($id);
        return view('admin.penanggung_jawab_ujian.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'jabatan' => 'required|string|max:255',
        ]);

        $item = PenanggungJawabUjian::findOrFail($id);
        $item->update($request->all());

        return redirect()->route('admin.penanggung-jawab.index')
            ->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $item = PenanggungJawabUjian::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.penanggung-jawab.index')
            ->with('success', 'Data berhasil dihapus.');
    }
}
