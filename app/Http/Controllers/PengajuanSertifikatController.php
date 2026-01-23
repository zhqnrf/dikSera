<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PengajuanSertifikat;
use App\Models\PenanggungJawabUjian;
use App\Models\JadwalWawancara;
use App\Models\PerawatLisensi;
use PhpOffice\PhpWord\TemplateProcessor;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PengajuanSertifikatController extends Controller
{
    /**
     * Menampilkan daftar pengajuan perawat.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Data pendukung untuk filter & dropdown
        $listSertifikat = PerawatLisensi::select('nama')->distinct()->pluck('nama');
        $pjs = PenanggungJawabUjian::all();

        // Query Utama
        $query = PengajuanSertifikat::where('user_id', $user->id)
            ->with(['lisensiLama', 'jadwalWawancara.pewawancara']);

        // --- Filter Search ---
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('lisensiLama', function ($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                    ->orWhere('nomor', 'LIKE', "%{$search}%");
            });
        }

        // --- Filter Status ---
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // --- Filter Nama Sertifikat ---
        if ($request->filled('sertifikat')) {
            $query->whereHas('lisensiLama', function ($q) use ($request) {
                $q->where('nama', $request->sertifikat);
            });
        }

        $pengajuan = $query->latest()->paginate(10)->withQueryString();

        // Ambil lisensi milik user (Active/Expired) untuk dropdown "Pengajuan Lama" di modal/form create
        $myLisensis = PerawatLisensi::where('user_id', $user->id)->orderBy('tgl_expired', 'desc')->get();

        return view('perawat.pengajuan.index', compact('user', 'pengajuan', 'pjs', 'listSertifikat', 'myLisensis'));
    }

    /**
     * Menyimpan pengajuan baru (Baru & Perpanjangan).
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // 1. Validasi Dasar (Wajib untuk semua jenis)
        $request->validate([
            'jenis_pengajuan' => 'required|in:baru,lama',
            'link_gdrive' => 'required|url',
        ]);

        // Siapkan array data dasar
        $dataSimpan = [
            'user_id' => $user->id,
            'status' => 'pending',
            'jenis_pengajuan' => $request->jenis_pengajuan,
            'link_gdrive' => $request->link_gdrive,
        ];

        // 2. Logika Percabangan Berdasarkan Jenis
        if ($request->jenis_pengajuan == 'lama') {
            // ==========================================
            // LOGIKA PERPANJANGAN (LAMA)
            // ==========================================
            $request->validate([
                'lisensi_id' => 'required|exists:perawat_lisensis,id',
                'file_rekomendasi' => 'required|mimes:pdf,jpg,jpeg,png|max:2048',
                'file_sertifikat_lama' => 'required|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);

            $lisensi = PerawatLisensi::findOrFail($request->lisensi_id);

            // Bersih-bersih data ujian lama user (Reset)
            if ($user->examResult) {
                $user->examResult->delete();
            }

            // Hapus pengajuan sebelumnya yang masih gantung/duplikat untuk lisensi ini
            PengajuanSertifikat::where('lisensi_lama_id', $lisensi->id)
                ->whereIn('status', ['pending', 'method_selected', 'exam_passed', 'interview_scheduled'])
                ->delete();

            // Upload File
            $pathRekomendasi = $request->file('file_rekomendasi')->store('dokumen/rekomendasi', 'public');
            $pathSertifLama = $request->file('file_sertifikat_lama')->store('dokumen/sertifikat_lama', 'public');

            // Lengkapi Data Simpan
            $dataSimpan['lisensi_lama_id'] = $lisensi->id;
            $dataSimpan['metode'] = $lisensi->metode ?? 'pg_only'; // Warisi metode lisensi lama
            $dataSimpan['file_rekomendasi'] = $pathRekomendasi;
            $dataSimpan['file_sertifikat_lama'] = $pathSertifLama;
            $dataSimpan['keterangan'] = 'Pengajuan Perpanjangan Lisensi';
        } else {
            // ==========================================
            // LOGIKA PENGAJUAN BARU
            // ==========================================
            $request->validate([
                'file_dokumen_baru' => 'required|mimes:pdf|max:5120', // PDF Max 5MB
            ]);

            // Upload File
            $pathDokumenBaru = $request->file('file_dokumen_baru')->store('dokumen/pengajuan_baru', 'public');

            // Lengkapi Data Simpan
            $dataSimpan['lisensi_lama_id'] = null;
            $dataSimpan['metode'] = null; // Null menandakan ini Pengajuan Baru (Admin akan menentukan nanti)
            $dataSimpan['file_dokumen_baru'] = $pathDokumenBaru;
            $dataSimpan['keterangan'] = 'Permohonan Lisensi Baru';
        }

        // 3. Simpan ke Database
        PengajuanSertifikat::create($dataSimpan);

        return redirect()->route('perawat.pengajuan.index')
            ->with('swal', ['icon' => 'success', 'title' => 'Berhasil', 'text' => 'Berkas pengajuan berhasil dikirim.']);
    }

    /**
     * Menyimpan jadwal wawancara yang dipilih peserta.
     */
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
     * Mencetak Sertifikat (PDF atau Word) berdasarkan metode.
     */
    public function printSertifikat($id)
    {
        $user = Auth::user();
        $pengajuan = PengajuanSertifikat::where('user_id', $user->id)
            ->with(['user.perawatProfile', 'user.pendidikanTerakhir', 'lisensiLama'])
            ->findOrFail($id);

        if ($pengajuan->status != 'completed') {
            return back()->with('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Sertifikat belum tersedia.']);
        }

        $profile = $pengajuan->user->perawatProfile;
        $lisensi = $pengajuan->lisensiLama;
        $namaLengkap = $profile->nama_lengkap ?? $user->name;

        // --- Setting Locale Tanggal ---
        Carbon::setLocale('id');

        // Tentukan Tanggal Dasar
        if (!empty($lisensi->tgl_mulai)) {
            $carbonDate = Carbon::parse($lisensi->tgl_mulai);
        } elseif (!empty($lisensi->tgl_diselenggarakan)) {
            $carbonDate = Carbon::parse($lisensi->tgl_diselenggarakan);
        } else {
            $carbonDate = Carbon::parse($pengajuan->updated_at);
        }

        $tglMulaiIndo = $carbonDate->translatedFormat('d F Y');
        $tglSelesaiIndo = $carbonDate->addYears(3)->translatedFormat('d F Y');

        $dateTerbit = !empty($lisensi->tgl_terbit) ? Carbon::parse($lisensi->tgl_terbit) : Carbon::now();
        $tglTerbitIndo = $dateTerbit->translatedFormat('d F Y');

        // --- Format Nomor Surat ---
        $tahunSurat = $dateTerbit->format('Y');
        $rawNomor = $lisensi->nomor;
        $cleanNomor = preg_replace('/[^0-9\-\.]/', '', $rawNomor);
        $nomorUrut = !empty($cleanNomor) ? $cleanNomor : '0';

        // Simpan sementara di object lisensi (Virtual Attribute)
        $lisensi->nomor_surat_full = "188/4150/418.25{$nomorUrut}/{$tahunSurat}";

        // --- LOGIKA CETAK ---

        // 1. KASUS INTERVIEW ONLY -> CETAK WORD
        if ($pengajuan->metode == 'interview_only') {
            $pathTemplate = storage_path('app/templates/template_serkom.docx');

            if (!file_exists($pathTemplate)) {
                return back()->with('swal', ['icon' => 'error', 'title' => 'Error', 'text' => 'Template Word tidak ditemukan.']);
            }

            $templateProcessor = new TemplateProcessor($pathTemplate);

            // Set Values
            $templateProcessor->setValue('nama', strtoupper($namaLengkap));
            $templateProcessor->setValue('nirp', $profile->nirp ?? '-');
            $templateProcessor->setValue('unit_kerja', strtoupper($user->unit_kerja ?? 'RSUD SLG'));
            $templateProcessor->setValue('bidang', strtoupper($lisensi->bidang ?? 'KEPERAWATAN'));
            $templateProcessor->setValue('nomor', $lisensi->nomor_surat_full);

            // Handling KFK (JSON or String)
            $valKfk = $lisensi->kfk;
            $stringKfk = $valKfk;
            if (is_array($valKfk)) {
                $stringKfk = implode(', ', $valKfk);
            } elseif (is_string($valKfk)) {
                $decoded = json_decode($valKfk, true);
                $stringKfk = (json_last_error() === JSON_ERROR_NONE && is_array($decoded))
                    ? implode(', ', $decoded)
                    : str_replace(['[', ']', '"'], '', $valKfk);
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

            // Save & Download
            $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $namaLengkap);
            $tempPath  = storage_path('app/public/Sertifikat_Wawancara_' . $cleanName . '.docx');
            $templateProcessor->saveAs($tempPath);

            return response()->download($tempPath)->deleteFileAfterSend(true);
        }

        // 2. KASUS LAINNYA -> CETAK PDF
        else {
            $dataPDF = [
                'user' => $user,
                'profile' => $profile,
                'lisensi' => $lisensi,
                'pengajuan' => $pengajuan,
                'tgl_ujian_indo' => $tglMulaiIndo,
                'tgl_expired_indo' => $tglSelesaiIndo,
                'tgl_terbit_indo' => $tglTerbitIndo,
                'nomor_surat' => $lisensi->nomor_surat_full
            ];

            $pdf = Pdf::loadView('perawat.pengajuan.sertifikat_pdf', $dataPDF);
            $pdf->setPaper('a4', 'landscape');

            $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $namaLengkap);
            return $pdf->download('Sertifikat_Kompetensi_' . $cleanName . '.pdf');
        }
    }
}
