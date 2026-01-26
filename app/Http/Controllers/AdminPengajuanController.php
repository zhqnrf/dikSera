<?php

namespace App\Http\Controllers;

use App\Models\JadwalWawancara;
use App\Models\PenanggungJawabUjian;
use Illuminate\Http\Request;
use App\Models\PengajuanSertifikat;
use App\Models\PerawatLisensi;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminPengajuanController extends Controller
{
    /**
     * MENAMPILKAN DAFTAR PENGAJUAN (DENGAN FILTER LAMA)
     */
    public function index(Request $request)
    {
        $listSertifikat = PerawatLisensi::select('nama')->distinct()->pluck('nama');
        $pjs = PenanggungJawabUjian::all();

        $query = PengajuanSertifikat::with(['user', 'lisensiLama', 'jadwalWawancara', 'user.examResult']);

        // Filter Pencarian (Nama/Email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Sertifikat
        if ($request->filled('sertifikat')) {
            $query->whereHas('lisensiLama', function ($q) use ($request) {
                $q->where('nama', $request->sertifikat);
            });
        }

        // Filter Ujian (Sudah/Belum)
        if ($request->filled('ujian')) {
            if ($request->ujian == 'sudah') {
                $query->whereHas('user.examResult');
            } elseif ($request->ujian == 'belum') {
                $query->whereDoesntHave('user.examResult');
            }
        }

        $pengajuan = $query->latest()->paginate(10)->withQueryString();

        return view('admin.pengajuan.index', compact('pengajuan', 'listSertifikat', 'pjs'));
    }

    public function show($id)
    {
        $pengajuan = PengajuanSertifikat::with([
            'user.examResult',
            'lisensiLama',
            'jadwalWawancara.pewawancara',
            'jadwalWawancara.penilaian'
        ])->findOrFail($id);

        return view('admin.pengajuan.show', compact('pengajuan'));
    }

    /**
     * 1. APPROVE BERKAS (LOGIKA BARU)
     * Menentukan apakah peserta harus ujian atau langsung wawancara
     */
    public function approve(Request $request, $id)
    {
        $pengajuan = PengajuanSertifikat::findOrFail($id);

        if ($pengajuan->status == 'pending') {
            // Priority: Input Admin -> Data User -> Default 'pg_interview'
            $metodeFix = $request->metode_pilihan ?? $pengajuan->metode ?? 'pg_interview';

            if ($metodeFix == 'interview_only') {
                $statusNext = 'exam_passed'; // Skip Ujian, Langsung siap wawancara
                $msg = 'Disetujui. Peserta Kredensialing langsung masuk tahap Jadwal Wawancara.';
            } else {
                $statusNext = 'method_selected'; // Wajib Ujian Tulis dulu
                $msg = 'Disetujui. Peserta Uji Kompetensi WAJIB mengerjakan Ujian Tulis.';
            }

            $pengajuan->update([
                'status'     => $statusNext,
                'metode'     => $metodeFix,
                'keterangan' => 'Verifikasi diterima. Metode: ' . ($metodeFix == 'interview_only' ? 'Kredensialing' : 'Uji Kompetensi')
            ]);

            return back()->with('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => $msg]);
        }

        return back()->with('swal', ['icon' => 'warning', 'title' => 'Info', 'text' => 'Pengajuan sudah diproses sebelumnya.']);
    }

    /**
     * REJECT PENGAJUAN
     */
    public function reject($id)
    {
        $pengajuan = PengajuanSertifikat::with('lisensiLama')->findOrFail($id);

        // Jika ini pengajuan baru yang ditolak, tandai lisensi lama (jika ada relasi aneh) sebagai rejected
        if ($pengajuan->jenis_pengajuan == 'baru' && $pengajuan->lisensiLama) {
            $pengajuan->lisensiLama->update(['status' => 'rejected']);
        }

        $pengajuan->update(['status' => 'rejected']);

        return back()->with('swal', ['icon' => 'success', 'title' => 'Ditolak', 'text' => 'Pengajuan telah ditolak.']);
    }

    /**
     * 2. UPDATE TANGGAL MASA BERLAKU (FITUR BARU)
     */
    public function updateDates(Request $request, $id)
    {
        $request->validate([
            'tgl_mulai_berlaku' => 'required|date',
            'tgl_akhir_berlaku' => 'required|date|after_or_equal:tgl_mulai_berlaku',
        ]);

        $pengajuan = PengajuanSertifikat::findOrFail($id);
        $pengajuan->update([
            'tgl_mulai_berlaku' => $request->tgl_mulai_berlaku,
            'tgl_akhir_berlaku' => $request->tgl_akhir_berlaku
        ]);

        // Jika status sudah completed, update data di Master Lisensi juga secara real-time
        if ($pengajuan->status == 'completed') {
            $this->finalizeLisensi($pengajuan);
        }

        return back()->with('swal', ['icon' => 'success', 'title' => 'Tersimpan', 'text' => 'Tanggal masa berlaku berhasil diperbarui.']);
    }

    /**
     * 3. APPROVE NILAI UJIAN (LOGIKA BARU)
     */
    public function approveExamScore($id)
    {
        $pengajuan = PengajuanSertifikat::findOrFail($id);
        $examResult = $pengajuan->user->examResult;

        if ($pengajuan->metode == 'interview_only') {
            return back()->with('swal', ['icon' => 'warning', 'title' => 'Info', 'text' => 'Metode Wawancara Saja tidak butuh validasi nilai.']);
        }

        if (!$examResult) {
            return back()->with('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Peserta belum mengerjakan ujian.']);
        }

        // Cek Nilai (Opsional: Batas KKM)
        if ($examResult->total_nilai < 75) {
            return back()->with('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Nilai peserta dibawah KKM (<75).']);
        }

        // Luluskan
        $examResult->update(['lulus' => 1]);

        // Pindah Status ke Siap Wawancara
        $pengajuan->update([
            'status' => 'exam_passed',
            'keterangan' => 'Lulus Ujian (Skor: ' . $examResult->total_nilai . '). Menunggu Wawancara.'
        ]);

        return back()->with('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Nilai disetujui. Status lanjut ke Wawancara.']);
    }

    /**
     * 4. COMPLETE PROCESS (FINALIZE)
     * Menerbitkan Lisensi Baru / Memperpanjang Lisensi Lama
     */
    public function completeProcess($id)
    {
        $pengajuan = PengajuanSertifikat::findOrFail($id);

        // 1. Ubah Status Pengajuan
        $pengajuan->update(['status' => 'completed']);

        // 2. Buat atau Update Lisensi di Tabel Master
        $this->finalizeLisensi($pengajuan);

        return back()->with('swal', ['icon' => 'success', 'title' => 'Selesai', 'text' => 'Proses selesai. Lisensi berhasil diterbitkan/diperbarui.']);
    }

    /**
     * PRIVATE HELPER: CREATE OR UPDATE LISENSI
     */
    private function finalizeLisensi($pengajuan)
    {
        $tglMulai = $pengajuan->tgl_mulai_berlaku ?? now();
        $tglAkhir = $pengajuan->tgl_akhir_berlaku ?? now()->addYears(3);

        // KASUS 1: PERPANJANGAN (Update data lama)
        if ($pengajuan->lisensiLama) {
            $pengajuan->lisensiLama->update([
                'status' => 'active',
                'tgl_terbit' => $tglMulai,
                'tgl_expired' => $tglAkhir,
                // Bisa update unit kerja atau KFK jika perlu
            ]);
        }
        // KASUS 2: PENGAJUAN BARU (Buat data baru di tabel perawat_lisensis)
        else {
            // Generate Nomor Surat Otomatis
            $bulanRomawi = $this->getRomawi(date('n'));
            $tahun = date('Y');
            $count = PerawatLisensi::count() + 1;
            // Format contoh: 445/001/PK/III/2026
            $nomorBaru = "445/" . sprintf("%03d", $count) . "/PK/" . $bulanRomawi . "/" . $tahun;

            $namaLisensi = 'Sertifikat Kompetensi (PK)';
            // Ambil KFK dari keterangan atau default
            $kfk = ['Perawat Klinis I'];

            // Create Record Master Lisensi
            $newLisensi = PerawatLisensi::create([
                'user_id' => $pengajuan->user_id,
                'nama' => $namaLisensi,
                'nomor' => $nomorBaru,
                'lembaga' => 'Komite Keperawatan',
                'bidang' => 'Keperawatan',
                'kfk' => $kfk,
                'metode' => $pengajuan->metode,
                'status' => 'active',
                'tgl_terbit' => $tglMulai,
                'tgl_expired' => $tglAkhir,
                'tgl_mulai' => $tglMulai,
                'tgl_diselenggarakan' => now(),
                'unit_kerja_saat_buat' => $pengajuan->user->unit_kerja ?? 'RSUD SLG'
            ]);

            // Link balik pengajuan ke lisensi baru ini
            $pengajuan->update(['lisensi_lama_id' => $newLisensi->id]);
        }
    }

    private function getRomawi($bulan)
    {
        $map = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
        return $map[$bulan] ?? 'I';
    }

    /**
     * DELETE PENGAJUAN & FILE FISIK
     */
    public function destroy($id)
    {
        $pengajuan = PengajuanSertifikat::findOrFail($id);

        // Hapus File Fisik
        if ($pengajuan->file_dokumen_baru) Storage::disk('public')->delete($pengajuan->file_dokumen_baru);
        if ($pengajuan->file_sertifikat_lama) Storage::disk('public')->delete($pengajuan->file_sertifikat_lama);
        if ($pengajuan->file_surat_rekomendasi) Storage::disk('public')->delete($pengajuan->file_surat_rekomendasi);

        $pengajuan->delete();

        return back()->with('swal', ['icon' => 'success', 'title' => 'Terhapus', 'text' => 'Data pengajuan berhasil dihapus permanen.']);
    }

    // ========================================================================
    // BULK ACTIONS (FITUR DARI KODE LAMA)
    // ========================================================================

    public function bulkApprove(Request $request)
    {
        $request->validate(['ids' => 'required|array']);

        $ids = $request->ids;
        $pengajuans = PengajuanSertifikat::whereIn('id', $ids)->where('status', 'pending')->get();
        $count = 0;

        foreach ($pengajuans as $pengajuan) {
            // Default jika metode null
            if (!$pengajuan->metode) $pengajuan->metode = 'pg_interview';

            if ($pengajuan->metode == 'interview_only') {
                $pengajuan->update(['status' => 'exam_passed']);
            } else {
                $pengajuan->update(['status' => 'method_selected']);
            }
            $count++;
        }

        return back()->with('success', "Berhasil menyetujui $count pengajuan terpilih.");
    }

    public function bulkApproveScore(Request $request)
    {
        $request->validate(['ids' => 'required|array']);

        $pengajuans = PengajuanSertifikat::with('user.examResult')
            ->whereIn('id', $request->ids)
            ->where('status', 'method_selected') // Hanya yg sedang ujian
            ->get();

        $count = 0;

        foreach ($pengajuans as $pengajuan) {
            if ($pengajuan->user && $pengajuan->user->examResult) {
                // Luluskan
                $pengajuan->user->examResult->update(['lulus' => 1]);
                // Update Status
                $pengajuan->update(['status' => 'exam_passed']);
                $count++;
            }
        }

        return back()->with('success', "Berhasil memproses nilai untuk $count peserta.");
    }

    public function bulkApproveInterview(Request $request)
    {
        $request->validate(['ids' => 'required|array']);

        $pengajuans = PengajuanSertifikat::with('jadwalWawancara')
            ->whereIn('id', $request->ids)
            ->where('status', 'interview_scheduled')
            ->get();

        $count = 0;

        foreach ($pengajuans as $pengajuan) {
            if ($pengajuan->jadwalWawancara && $pengajuan->jadwalWawancara->status == 'pending') {
                $pengajuan->jadwalWawancara->update(['status' => 'approved']);
                $count++;
            }
        }

        return back()->with('success', "Berhasil menyetujui $count jadwal wawancara.");
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array']);

        // Loop untuk hapus file fisik dulu (best practice)
        $pengajuans = PengajuanSertifikat::whereIn('id', $request->ids)->get();
        foreach ($pengajuans as $p) {
            if ($p->file_dokumen_baru) Storage::disk('public')->delete($p->file_dokumen_baru);
            if ($p->file_sertifikat_lama) Storage::disk('public')->delete($p->file_sertifikat_lama);
        }

        $count = PengajuanSertifikat::whereIn('id', $request->ids)->delete();

        return back()->with('success', "Berhasil menghapus $count data terpilih.");
    }

    // ========================================================================
    // EXPORT EXCEL (FITUR DARI KODE LAMA)
    // ========================================================================
    public function exportJadwal()
    {
        $data = JadwalWawancara::with(['pengajuan.user', 'pewawancara'])
            ->whereIn('status', ['approved', 'completed'])
            ->latest()
            ->get();

        $fileName = 'Rekap_Jadwal_Wawancara_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Nama Perawat', 'Unit Kerja', 'Nama Pewawancara', 'Tanggal', 'Jam', 'Lokasi', 'Deskripsi', 'Status']);

            foreach ($data as $key => $row) {
                $waktu = \Carbon\Carbon::parse($row->waktu_wawancara);
                fputcsv($file, [
                    $key + 1,
                    $row->pengajuan->user->name ?? '-',
                    $row->pengajuan->user->unit_kerja ?? '-',
                    $row->pewawancara->nama ?? '-',
                    $waktu->format('Y-m-d'),
                    $waktu->format('H:i'),
                    $row->lokasi,
                    $row->deskripsi_skill ?? '-',
                    ucfirst($row->status)
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
