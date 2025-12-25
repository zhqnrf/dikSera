<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PengajuanSertifikat;
use App\Models\PenanggungJawabUjian;
use App\Models\JadwalWawancara;
use App\Models\PerawatLisensi;
// Import Library Penting
use PhpOffice\PhpWord\TemplateProcessor; // Untuk Word
use Barryvdh\DomPDF\Facade\Pdf;          // Untuk PDF
use Carbon\Carbon;

class PengajuanSertifikatController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $listSertifikat = PerawatLisensi::select('nama')->distinct()->pluck('nama');

        $query = PengajuanSertifikat::where('user_id', $user->id)
            ->with(['lisensiLama', 'jadwalWawancara.pewawancara']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('lisensiLama', function ($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                    ->orWhere('nomor', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('sertifikat')) {
            $query->whereHas('lisensiLama', function ($q) use ($request) {
                $q->where('nama', $request->sertifikat);
            });
        }

        $pengajuan = $query->latest()->paginate(10)->withQueryString();
        $pjs = PenanggungJawabUjian::all();

        return view('perawat.pengajuan.index', compact('user', 'pengajuan', 'pjs', 'listSertifikat'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $lisensi = PerawatLisensi::findOrFail($request->lisensi_id);

        // 1. Ambil metode yang disetting di Master Lisensi
        $metodeTarget = $lisensi->metode_perpanjangan ?? 'pg_only';

        // =========================================================================
        // LOGIKA BARU: VALIDASI SYARAT INTERVIEW ONLY
        // =========================================================================
        if ($metodeTarget == 'interview_only') {
            // Cek riwayat: Apakah user pernah LULUS (completed) dengan metode SELAIN interview_only?
            // (Artinya dia sudah pernah PG Only atau PG + Wawancara)
            $syaratTerpenuhi = PengajuanSertifikat::where('user_id', $user->id)
                ->where('status', 'completed') // Wajib status selesai
                ->where('metode', '!=', 'interview_only') // Bukan wawancara saja (berarti mengandung PG)
                ->exists();

            if (!$syaratTerpenuhi) {
                // OPSI 1: Tolak pengajuan (Tampilkan Error)
                return back()->with('swal', [
                    'icon' => 'error',
                    'title' => 'Syarat Belum Terpenuhi',
                    'text' => 'Metode "Hanya Wawancara" hanya dapat dipilih jika Anda sudah pernah lulus metode Pilihan Ganda atau Gabungan sebelumnya.'
                ]);

                // OPSI 2 (Opsional): Jika ingin otomatis dipaksa jadi PG Only, hapus return di atas dan uncomment baris bawah:
                // $metodeTarget = 'pg_only';
            }
        }
        // =========================================================================

        // 2. Cek apakah sedang ada pengajuan berjalan (Anti-Spam/Duplicate)
        $exists = PengajuanSertifikat::where('user_id', $user->id)
            ->where('lisensi_lama_id', $request->lisensi_id)
            ->whereIn('status', ['pending', 'method_selected', 'exam_passed', 'interview_scheduled'])
            ->exists();

        if ($exists) {
            return back()->with('swal', ['icon' => 'warning', 'title' => 'Ups', 'text' => 'Pengajuan untuk lisensi ini sedang diproses.']);
        }

        // 3. Simpan Data
        PengajuanSertifikat::create([
            'user_id' => $user->id,
            'lisensi_lama_id' => $request->lisensi_id,
            'status' => 'pending',
            'metode' => $metodeTarget // Gunakan variabel yang sudah divalidasi
        ]);

        return redirect()->route('perawat.pengajuan.index')
            ->with('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Permintaan dikirim. Metode evaluasi telah ditentukan sistem.']);
    }

    public function storeWawancara(Request $request, $id)
    {
        $pengajuan = PengajuanSertifikat::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'penanggung_jawab_id' => 'required',
            'tgl_wawancara' => 'required|date',
            'jam_wawancara' => 'required',
            'lokasi_wawancara' => 'required'
        ]);

        $waktu = $request->tgl_wawancara . ' ' . $request->jam_wawancara;

        JadwalWawancara::create([
            'pengajuan_sertifikat_id' => $pengajuan->id,
            'penanggung_jawab_id' => $request->penanggung_jawab_id,
            'waktu_wawancara' => $waktu,
            'lokasi' => $request->lokasi_wawancara,
            'status' => 'pending'
        ]);

        $pengajuan->update(['status' => 'interview_scheduled']);

        return back()->with('swal', ['icon' => 'success', 'title' => 'Tersimpan', 'text' => 'Jadwal wawancara berhasil diajukan.']);
    }

    /**
     * FUNGSI PRINT SERTIFIKAT (WORD atau PDF)
     * Logika:
     * - Interview Only -> Word (.docx)
     * - PG / PG+Interview -> PDF (.pdf)
     */
  public function printSertifikat($id)
{
    // 1. Ambil Data
    $user = Auth::user();
    $pengajuan = PengajuanSertifikat::where('user_id', $user->id)
        ->with(['user.perawatProfile', 'user.pendidikanTerakhir', 'lisensiLama'])
        ->findOrFail($id);

    if ($pengajuan->status != 'completed') {
        return back()->with('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Sertifikat belum tersedia.']);
    }

    // Persiapan Data Umum
    $profile = $pengajuan->user->perawatProfile;
    $lisensi = $pengajuan->lisensiLama;
    $namaLengkap = $profile->nama_lengkap ?? $user->name;

    // Setup Tanggal
    Carbon::setLocale('id');

    // Logic Tanggal Mulai
    if (!empty($lisensi->tgl_mulai)) {
        $carbonDate = Carbon::parse($lisensi->tgl_mulai);
    } elseif (!empty($lisensi->tgl_diselenggarakan)) {
         $carbonDate = Carbon::parse($lisensi->tgl_diselenggarakan);
    } else {
        $carbonDate = Carbon::parse($pengajuan->updated_at);
    }

    $tglMulaiIndo = $carbonDate->translatedFormat('d F Y');
    $tglSelesaiIndo = $carbonDate->addYears(3)->translatedFormat('d F Y');

    // Ambil tanggal terbit
    $dateTerbit = !empty($lisensi->tgl_terbit) ? Carbon::parse($lisensi->tgl_terbit) : Carbon::now();
    $tglTerbitIndo = $dateTerbit->translatedFormat('d F Y');

    // ============================================================
    // [PERBAIKAN] LOGIKA FORMAT NOMOR SURAT
    // Target: 188/4150/418.25.(Hanya Angka)/Tahun
    // ============================================================

    $tahunSurat = $dateTerbit->format('Y');

    // AMBIL DATA MENTAH
    $rawNomor = $lisensi->nomor;
    $cleanNomor = preg_replace('/[^0-9\-\.]/', '', $rawNomor);
    $nomorUrut = !empty($cleanNomor) ? $cleanNomor : '0';
    $nomorSuratFull = "188/4150/418.25{$nomorUrut}/{$tahunSurat}";
    $lisensi->nomor = $nomorSuratFull;
    if ($pengajuan->metode == 'interview_only') {

        // --- WORD ---
        $pathTemplate = storage_path('app/templates/template_serkom.docx');
        if (!file_exists($pathTemplate)) return back()->with('swal', ['icon' => 'error', 'title' => 'Error', 'text' => 'Template Word hilang.']);

        $templateProcessor = new TemplateProcessor($pathTemplate);

        $templateProcessor->setValue('nama', strtoupper($namaLengkap));
        $templateProcessor->setValue('nirp', $profile->nirp ?? '-');
        $templateProcessor->setValue('unit_kerja', strtoupper($user->unit_kerja ?? 'RSUD SLG'));
        $templateProcessor->setValue('bidang', strtoupper($lisensi->bidang ?? 'KEPERAWATAN'));

        // Input Nomor Bersih
        $templateProcessor->setValue('nomor', $lisensi->nomor);

        // KFK Cleaning
        $valKfk = $lisensi->kfk;
        $stringKfk = $valKfk;
        if (is_array($valKfk)) { $stringKfk = implode(', ', $valKfk); }
        elseif (is_string($valKfk)) {
            $decoded = json_decode($valKfk, true);
            $stringKfk = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? implode(', ', $decoded) : str_replace(['[', ']', '"'], '', $valKfk);
        }
        $finalKfk = !empty($stringKfk) ? $stringKfk : $lisensi->nama;
        $templateProcessor->setValue('kfk', strtoupper($finalKfk));

        // Pendidikan & Tanggal
        $pendidikanData = $pengajuan->user->pendidikanTerakhir;
        $txtPendidikan = $pendidikanData ? trim($pendidikanData->jenjang . ' ' . $pendidikanData->jurusan) : '-';
        $templateProcessor->setValue('pendidikan', strtoupper($txtPendidikan));
        $templateProcessor->setValue('tgl_mulai', $tglMulaiIndo);
        $templateProcessor->setValue('tgl_selesai', $tglSelesaiIndo);
        $templateProcessor->setValue('tgl_terbit', $tglTerbitIndo);

        $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $namaLengkap);
        $tempPath  = storage_path('app/public/Sertifikat_Wawancara_' . $cleanName . '.docx');
        $templateProcessor->saveAs($tempPath);

        return response()->download($tempPath)->deleteFileAfterSend(true);

    } else {

        // --- PDF ---
        $dataPDF = [
            'user' => $user,
            'profile' => $profile,
            'lisensi' => $lisensi, // Sudah bersih
            'pengajuan' => $pengajuan,
            'tgl_ujian_indo' => $tglMulaiIndo,
            'tgl_expired_indo' => $tglSelesaiIndo,
            'tgl_terbit_indo' => $tglTerbitIndo
        ];

        $pdf = Pdf::loadView('perawat.pengajuan.sertifikat_pdf', $dataPDF);
        $pdf->setPaper('a4', 'landscape');

        $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $namaLengkap);
        return $pdf->download('Sertifikat_Kompetensi_' . $cleanName . '.pdf');
    }
}
}
