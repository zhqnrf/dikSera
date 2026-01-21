<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSertifikat;
use App\Models\PerawatLisensi;
use App\Models\PerawatPekerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // <--- TAMBAHKAN INI
use Barryvdh\DomPDF\Facade\Pdf; // <--- TAMBAHKAN INI

class PerawatLisensiController extends Controller
{
    private function checkEligibility($userId, $requestedMetode)
    {
        $latestJob = PerawatPekerjaan::where('user_id', $userId)->orderBy('tahun_mulai', 'desc')->first();

        if (!$latestJob) return ['allowed' => false, 'message' => 'Lengkapi Riwayat Pekerjaan dahulu.'];

        $currentUnit = $latestJob->unit_kerja;

        // Gunakan 'metode' sesuai nama kolom DB
        $existingLisensi = PerawatLisensi::where('user_id', $userId)
            ->where('unit_kerja_saat_buat', $currentUnit)
            ->where('metode', $requestedMetode)
            ->exists();

        if ($existingLisensi) {
            return ['allowed' => false, 'message' => "Anda sudah membuat pengajuan di unit $currentUnit."];
        }

        return ['allowed' => true, 'unit_kerja' => $currentUnit];
    }

    public function lisensiCreate($metode)
    {
        if (!in_array($metode, ['interview_only', 'pg_interview'])) {
            return back()->with('swal', ['icon' => 'error', 'title' => 'Error', 'text' => 'Metode salah']);
        }
        $user = Auth::user();
        $check = $this->checkEligibility($user->id, $metode);

        if (!$check['allowed']) {
            return redirect()->route('perawat.lisensi.index')->with('swal', ['icon' => 'error', 'title' => 'Akses Ditolak', 'text' => $check['message']]);
        }

        return view('perawat.dokumen.lisensi.create', [
            'user' => $user,
            'unit_kerja' => $check['unit_kerja'],
            'metode' => $metode
        ]);
    }

    public function lisensiStore(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:100',
            'lembaga' => 'required|string|max:100',
            'bidang' => 'required|string|max:100',
            'kfk' => 'required|array',
            'tgl_mulai' => 'required|date',
            'tgl_diselenggarakan' => 'required|date',
            'tgl_terbit' => 'required|date',
            'tgl_expired' => 'required|date',
            'metode_perpanjangan' => 'required|in:interview_only,pg_interview', // Validasi input form
            'dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $check = $this->checkEligibility($user->id, $request->metode_perpanjangan);
        if (!$check['allowed']) {
            return redirect()->route('perawat.lisensi.index')->with('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => $check['message']]);
        }

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
            'unit_kerja_saat_buat' => $check['unit_kerja'],

            // PENTING: Map input 'metode_perpanjangan' ke kolom DB 'metode'
            'metode' => $request->metode_perpanjangan,

            // Status Awal Pending
            'status' => 'pending',
        ];

        if ($request->hasFile('dokumen')) {
            $data['file_path'] = $request->file('dokumen')->store('perawat/dokumen/lisensi', 'public');
        }

        $lisensiBaru = PerawatLisensi::create($data);

        // Buat Tiket Pengajuan (Metode NULL = New Submission)
        PengajuanSertifikat::create([
            'user_id' => $user->id,
            'lisensi_lama_id' => $lisensiBaru->id,
            'metode' => null, // NULL untuk menandai Pengajuan Baru
            'status' => 'pending',
            'keterangan' => 'Permohonan Lisensi Baru (Menunggu Verifikasi Admin)'
        ]);

        return redirect()->route('perawat.lisensi.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil Diajukan',
            'text' => 'Data lisensi berhasil disimpan. Menunggu persetujuan Admin.'
        ]);
    }

    public function downloadHasil($id)
    {
        // 1. Cari data pengajuan
        $pengajuan = PengajuanSertifikat::with(['jadwalWawancara.penilaian', 'user', 'lisensiLama'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        // 2. LOGIKA KREDENSIALING (Interview Only)
        if ($pengajuan->metode == 'interview_only') {

            // Ambil path file
            $filePath = $pengajuan->jadwalWawancara->penilaian->file_hasil ?? null;

            // Cek keberadaan file
            if ($filePath && Storage::disk('public')->exists($filePath)) {

                // --- PERBAIKAN DISINI AGAR TIDAK MERAH ---
                // Kita gunakan helper storage_path untuk mendapatkan full path file
                $fullPath = storage_path('app/public/' . $filePath);

                // Gunakan response()->download() yang lebih dikenali VS Code
                return response()->download($fullPath, 'Surat_Keputusan_Kredensial.pdf');
            } else {
                return back()->with('swal', [
                    'icon' => 'error',
                    'title' => 'Gagal',
                    'text' => 'File dokumen SK belum tersedia atau dihapus.'
                ]);
            }
        }

        // 3. LOGIKA UJI KOMPETENSI / LISENSI BARU
        else {
            $data = [
                'nama' => $pengajuan->user->name,
                'nomor_lisensi' => $pengajuan->lisensiLama->nomor ?? 'BARU',
                'tanggal_lulus' => $pengajuan->updated_at->format('d F Y'),
            ];

            $pdf = Pdf::loadView('perawat.dokumen.pdf_sertifikat', ['data' => $data]);
            return $pdf->stream('Sertifikat_Kompetensi.pdf');
        }
    }
}
