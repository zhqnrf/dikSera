<?php

namespace App\Http\Controllers;

use App\Models\PerawatLisensi;
use App\Models\PerawatPekerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerawatLisensiController extends Controller
{
    /**
     * Cek apakah user boleh membuat lisensi untuk UNIT & METODE tertentu
     */
    private function checkEligibility($userId, $requestedMetode)
    {
        // 1. Ambil Unit Kerja Terakhir dari DRH
        $latestJob = PerawatPekerjaan::where('user_id', $userId)
            ->orderBy('tahun_mulai', 'desc')
            ->first();

        if (!$latestJob) {
            return [
                'allowed' => false, 
                'message' => 'Silakan lengkapi Riwayat Pekerjaan (Unit Kerja) terlebih dahulu.'
            ];
        }

        $currentUnit = $latestJob->unit_kerja;

        // 2. Cek Duplikasi Spesifik (User + Unit Sama + Metode Sama)
        $existingLisensi = PerawatLisensi::where('user_id', $userId)
            ->where('unit_kerja_saat_buat', $currentUnit) // Di unit yang sama
            ->where('metode_perpanjangan', $requestedMetode) // Dengan metode yang sama
            ->exists();

        if ($existingLisensi) {
            // Ubah format string biar enak dibaca user
            $namaMetode = ($requestedMetode == 'pg_interview') ? 'PG + Wawancara' : 'Wawancara Saja';
            
            return [
                'allowed' => false, 
                'message' => "Anda sudah membuat pengajuan ($namaMetode) di unit $currentUnit. Tidak bisa dobel metode yang sama di satu unit."
            ];
        }

        return [
            'allowed' => true, 
            'unit_kerja' => $currentUnit
        ];
    }

    /**
     * Menampilkan Form
     * URL contoh: /lisensi/create/pg_interview atau /lisensi/create/interview_only
     */
    public function lisensiCreate($metode)
    {
        // Validasi input URL agar tidak sembarangan
        if (!in_array($metode, ['interview_only', 'pg_interview'])) {
            return back()->with('swal', ['icon' => 'error', 'title' => 'Error', 'text' => 'Jenis metode tidak dikenali']);
        }

        $user = Auth::user();

        // Panggil fungsi cek dengan parameter metode yang diminta
        $check = $this->checkEligibility($user->id, $metode);

        if (!$check['allowed']) {
            return redirect()->route('perawat.lisensi.index')->with('swal', [
                'icon' => 'error',
                'title' => 'Akses Ditolak',
                'text' => $check['message']
            ]);
        }

        return view('perawat.dokumen.lisensi.create', [
            'user' => $user,
            'unit_kerja' => $check['unit_kerja'],
            'metode' => $metode // Kirim ke view buat input hidden
        ]);
    }

    public function lisensiStore(Request $request)
    {
        $user = Auth::user();

        // Validasi Input Dasar
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

        // Cek lagi sebelum simpan (Security Layer)
        // Gunakan metode dari request form
        $check = $this->checkEligibility($user->id, $request->metode_perpanjangan);

        if (!$check['allowed']) {
            return redirect()->route('perawat.lisensi.index')->with('swal', [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => $check['message']
            ]);
        }

        // Generate nomor otomatis
        $lastId = PerawatLisensi::max('id') ?? 0;
        $urutan = $lastId + 1;
        $nomorOtomatis = strtoupper($request->nama) . '-' . date('Y') . '-' . sprintf('%04d', $urutan);

        $data = [
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
            'unit_kerja_saat_buat' => $check['unit_kerja'], // Unit didapat dari fungsi checkEligibility
        ];

        // Upload File jika ada
        if ($request->hasFile('dokumen')) {
            $data['file_path'] = $request->file('dokumen')->store('perawat/dokumen/lisensi', 'public');
        }

        PerawatLisensi::create($data);

        return redirect()->route('perawat.lisensi.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Lisensi berhasil dibuat'
        ]);
    }
}