<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerawatLisensi;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AdminLisensiController extends Controller
{
    public function lisensiCreate()
    {
        $users = User::where('role', 'perawat')->orderBy('name', 'asc')->get();
        return view('admin.lisensi.create', compact('users'));
    }

    public function lisensiStore(Request $request)
    {
        // 1. Validasi (Hapus 'nomor' dari sini)
        $request->validate([
            'user_ids'            => 'required|array',
            'user_ids.*'          => 'exists:users,id',
            'metode_perpanjangan' => 'required|in:pg_only,pg_interview',
            'nama'                => 'required|string|max:100',
            'lembaga'             => 'required|string|max:100',
            // 'nomor' dihapus karena auto-generate
            'tgl_terbit'          => 'required|date',
            'tgl_expired'         => 'required|date',
        ]);

        // Ambil data umum
        $commonData = $request->except(['_token', 'user_ids']);

        // Cari ID terakhir untuk penomoran awal
        // Jika belum ada data, mulai dari 0
        $lastId = PerawatLisensi::max('id') ?? 0;

        $count = 0;
        foreach ($request->user_ids as $index => $userId) {

            // LOGIKA NOMOR OTOMATIS BERURUTAN
            // Urutan = ID Terakhir + 1 + Index Loop saat ini
            $urutan = $lastId + 1 + $count;

            // Format: NAMA-TAHUN-URUTAN (Contoh: STR-2025-0001)
            // strtoupper untuk huruf besar, sprintf untuk padding 0 (0001)
            $nomorOtomatis = strtoupper($request->nama) . '-' . date('Y') . '-' . sprintf('%04d', $urutan);

            // Gabungkan data
            $data = array_merge($commonData, [
                'user_id' => $userId,
                'nomor'   => $nomorOtomatis // Masukkan nomor otomatis ke sini
            ]);

            PerawatLisensi::create($data);
            $count++;
        }

        return redirect()->route('admin.lisensi.index')->with('swal', [
            'icon'  => 'success',
            'title' => 'Berhasil',
            'text'  => "Lisensi berhasil dibuat untuk $count perawat dengan nomor urut otomatis."
        ]);
    }

    public function lisensiIndex()
    {
        $data = PerawatLisensi::paginate(10);
        return view('admin.lisensi.index', compact('data'));
    }

    public function lisensiEdit($id)
    {
        $data = PerawatLisensi::findOrFail($id);
        $users = User::where('role', 'perawat')->orderBy('name', 'asc')->get();
        return view('admin.lisensi.edit', compact('data', 'users'));
    }

    public function lisensiUpdate(Request $request, $id)
    {
        $lisensi = PerawatLisensi::findOrFail($id);

        // Untuk update, nomor boleh diubah manual jika perlu, atau biarkan validasi string biasa
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'nama'        => 'required|string|max:100',
            'lembaga'     => 'required|string|max:100',
            'nomor'       => 'required|string|max:100', // Update tetap butuh nomor (edit manual)
            'tgl_terbit'  => 'required|date',
            'tgl_expired' => 'required|date',
            'dokumen'     => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = $request->except(['dokumen', '_token', '_method']);
        $data['user_id'] = $request->user_id;

        if ($request->hasFile('dokumen')) {
            if ($lisensi->file_path && Storage::disk('public')->exists($lisensi->file_path)) {
                Storage::disk('public')->delete($lisensi->file_path);
            }
            $data['file_path'] = $request->file('dokumen')->store('perawat/dokumen/lisensi', 'public');
        }

        $lisensi->update($data);
        return redirect()->route('admin.lisensi.index')->with('swal', ['icon'=>'success', 'title'=>'Berhasil', 'text'=>'Lisensi diperbarui.']);
    }

    public function lisensiDestroy($id)
    {
        $data = PerawatLisensi::findOrFail($id);

        if ($data->file_path && Storage::disk('public')->exists($data->file_path)) {
            Storage::disk('public')->delete($data->file_path);
        }
        $data->delete();
        return redirect()->route('admin.lisensi.index')->with('swal', ['icon'=>'success', 'title'=>'Berhasil', 'text'=>'Lisensi dihapus.']);
    }
}
