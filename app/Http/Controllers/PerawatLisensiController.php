<?php

namespace App\Http\Controllers;

use App\Models\PerawatLisensi;
use App\Models\PerawatPekerjaan;
use Illuminate\Http\Request;

class PerawatLisensiController extends Controller
{
    private function canCreateLisensi($userId)
    {
        $latestJob = PerawatPekerjaan::where('user_id', $userId)
            ->orderBy('tahun_mulai', 'desc')
            ->first();

        if (!$latestJob) {
            return ['can' => false, 'message' => 'Silakan daftarkan pekerjaan terlebih dahulu'];
        }

        $currentUnit = $latestJob->unit_kerja;

        // Cek lisensi yang sudah ada dengan unit kerja sama
        $existingLisensi = PerawatLisensi::where('user_id', $userId)
            ->where('unit_kerja_saat_buat', $currentUnit)
            ->exists();

        if ($existingLisensi) {
            return ['can' => false, 'message' => 'Anda sudah membuat lisensi untuk unit kerja saat ini. Ganti unit kerja untuk membuat lisensi baru'];
        }

        return ['can' => true, 'unit_kerja' => $currentUnit];
    }

    public function lisensiCreate()
    {
        $user = auth()->user();
        $check = $this->canCreateLisensi($user->id);

        if (!$check['can']) {
            return back()->with('swal', [
                'icon' => 'error',
                'title' => 'Tidak Dapat Membuat Lisensi',
                'text' => $check['message']
            ]);
        }

        return view('perawat.dokumen.lisensi.create', [
            'user' => $user,
            'unit_kerja' => $check['unit_kerja']
        ]);
    }

    public function lisensiStore(Request $request)
    {
        $user = auth()->user();
        $check = $this->canCreateLisensi($user->id);

        if (!$check['can']) {
            return back()->with('swal', [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => $check['message']
            ]);
        }

        $request->validate([
            'nama' => 'required|string|max:100',
            'lembaga' => 'required|string|max:100',
            'bidang' => 'required|string|max:100',
            'kfk' => 'required|array',
            'tgl_mulai' => 'required|date',
            'tgl_diselenggarakan' => 'required|date',
            'tgl_terbit' => 'required|date',
            'tgl_expired' => 'required|date',
            'metode_perpanjangan' => 'required|in:interview_only,pg_interview',
        ]);

        // Generate nomor otomatis
        $lastId = PerawatLisensi::max('id') ?? 0;
        $urutan = $lastId + 1;
        $nomorOtomatis = strtoupper($request->nama) . '-' . date('Y') . '-' . sprintf('%04d', $urutan);

        PerawatLisensi::create([
            'user_id' => $user->id,
            'nomor' => $nomorOtomatis,
            'nama' => $request->nama,
            'lembaga' => $request->lembaga,
            'bidang' => $request->bidang,
            'kfk' => json_encode($request->kfk),
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_diselenggarakan' => $request->tgl_diselenggarakan,
            'tgl_terbit' => $request->tgl_terbit,
            'tgl_expired' => $request->tgl_expired,
            'metode_perpanjangan' => $request->metode_perpanjangan,
            'unit_kerja_saat_buat' => $check['unit_kerja'], // SIMPAN UNIT KERJA
        ]);

        return redirect()->route('perawat.lisensi.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Lisensi berhasil dibuat'
        ]);
    }
}