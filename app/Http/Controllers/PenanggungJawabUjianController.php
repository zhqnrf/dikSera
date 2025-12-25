<?php

namespace App\Http\Controllers;

use App\Models\PenanggungJawabUjian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenanggungJawabUjianController extends Controller
{
    public function index()
    {
        $data = PenanggungJawabUjian::with('user')->paginate(10);
        return view('admin.penanggung_jawab_ujian.index', compact('data'));
    }

    public function create()
    {
        return view('admin.penanggung_jawab_ujian.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi Input (Hapus validasi type)
        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'no_hp'    => 'required|string|max:20',
            'jabatan'  => 'required|string|max:255',
            // 'type' dihapus
        ]);

        DB::beginTransaction();

        try {
            // A. Buat Akun User Baru
            $user = User::create([
                'name'     => $request->nama,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'pewawancara',
            ]);

            // B. Buat Data Profil Penanggung Jawab
            PenanggungJawabUjian::create([
                'user_id' => $user->id,
                'nama'    => $request->nama,
                'no_hp'   => $request->no_hp,
                'jabatan' => $request->jabatan,
                // 'type' dihapus
            ]);

            DB::commit();

            return redirect()->route('admin.penanggung-jawab.index')
                ->with('success', 'Akun & Data Penanggung Jawab berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $item = PenanggungJawabUjian::with('user')->findOrFail($id);
        return view('admin.penanggung_jawab_ujian.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = PenanggungJawabUjian::findOrFail($id);

        // 1. Validasi Update (Hapus validasi type)
        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . ($item->user_id ?? 0),
            'password' => 'nullable|string|min:6',
            'no_hp'    => 'required|string|max:20',
            'jabatan'  => 'required|string|max:255',
            // 'type' dihapus
        ]);

        DB::beginTransaction();

        try {
            // A. Update Data Profil
            $item->update([
                'nama'    => $request->nama,
                'no_hp'   => $request->no_hp,
                'jabatan' => $request->jabatan,
                // 'type' dihapus
            ]);

            // B. Update Data Akun User
            if ($item->user) {
                $dataUser = [
                    'name'  => $request->nama,
                    'email' => $request->email
                ];

                if ($request->filled('password')) {
                    $dataUser['password'] = Hash::make($request->password);
                }

                $item->user->update($dataUser);
            }

            DB::commit();

            return redirect()->route('admin.penanggung-jawab.index')
                ->with('success', 'Data & Akun Login berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $item = PenanggungJawabUjian::findOrFail($id);

        DB::beginTransaction();
        try {
            if ($item->user) {
                $item->user->delete();
            }

            $item->delete();

            DB::commit();
            return redirect()->route('admin.penanggung-jawab.index')
                ->with('success', 'Data & Akun berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data.');
        }
    }
}
