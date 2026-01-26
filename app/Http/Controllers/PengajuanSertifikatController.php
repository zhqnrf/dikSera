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
    public function index(Request $request)
    {
        $user = Auth::user();
        $listSertifikat = PerawatLisensi::select('nama')->distinct()->pluck('nama');

        $query = PengajuanSertifikat::where('user_id', $user->id)
            ->with(['lisensiLama', 'jadwalWawancara.pewawancara']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('jenis_pengajuan', 'LIKE', "%{$search}%")
                    ->orWhereHas('lisensiLama', function ($subQ) use ($search) {
                        $subQ->where('nama', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pengajuan = $query->latest()->paginate(10)->withQueryString();
        $pjs = PenanggungJawabUjian::all();

        return view('perawat.pengajuan.index', compact('user', 'pengajuan', 'pjs', 'listSertifikat'));
    }

    /**
     * [FIXED] LOGIKA CREATE
     * Cek apakah user sudah punya lisensi UNTUK METODE TERSEBUT.
     */
    public function create($metode = 'pg_interview')
    {
        $user = Auth::user();

        // Cek Spesifik: Apakah user sudah punya lisensi aktif/expired dengan metode ini?
        // Contoh: Jika mau Kredensialing (interview_only), cek apakah sudah punya data kredensialing.
        // Data Ujikom (pg_interview) TIDAK akan mempengaruhi pengecekan ini.
        $sudahPunyaLisensi = PerawatLisensi::where('user_id', $user->id)
            ->where('metode', $metode)
            ->whereIn('status', ['active', 'expired'])
            ->exists();

        // Arahkan ke view di folder dokumen/lisensi sesuai request
        return view('perawat.dokumen.lisensi.create', compact('sudahPunyaLisensi', 'metode'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // 1. VALIDASI INPUT
        $request->validate([
            'jenis_pengajuan' => 'required|in:baru,perpanjangan',
            'link_gdrive'     => 'required|url',
            'kfk'             => 'required|array',
            'metode'          => 'required', // Pastikan metode terkirim dari form
        ]);

        // 2. VALIDASI DUPLIKAT (Backend Protection)
        if ($request->jenis_pengajuan == 'baru') {
            $cekLisensi = PerawatLisensi::where('user_id', $user->id)
                ->where('metode', $request->metode) // Cek spesifik metode
                ->whereIn('status', ['active', 'expired'])
                ->exists();

            if ($cekLisensi) {
                return back()->with('swal', ['icon' => 'error', 'title' => 'Error', 'text' => 'Anda sudah memiliki dokumen jenis ini. Harap pilih Perpanjangan.']);
            }
        }

        // 3. VALIDASI FILE DINAMIS
        if ($request->jenis_pengajuan == 'baru') {
            // Jika Baru -> Butuh 1 File PDF Lengkap
            $request->validate(['file_dokumen_baru' => 'required|mimes:pdf|max:5120']);
        } else {
            // Jika Perpanjangan -> Butuh Sertifikat Lama & Rekomendasi
            $request->validate([
                'file_sertifikat_lama'   => 'required|mimes:pdf,jpg,jpeg,png|max:2048',
                'file_surat_rekomendasi' => 'required|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);
        }

        // 4. BERSIHKAN PENGAJUAN GANTUNG (Hanya untuk metode yang sama)
        PengajuanSertifikat::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('metode', $request->metode)
            ->delete();

        // Reset Nilai Ujian jika metode butuh ujian
        if ($request->metode != 'interview_only' && $user->examResult) {
            $user->examResult->delete();
        }

        // 5. UPLOAD FILE
        $pathBaru = $request->hasFile('file_dokumen_baru') ? $request->file('file_dokumen_baru')->store('dokumen_pengajuan', 'public') : null;
        $pathLama = $request->hasFile('file_sertifikat_lama') ? $request->file('file_sertifikat_lama')->store('sertifikat_lama', 'public') : null;
        $pathRekom = $request->hasFile('file_surat_rekomendasi') ? $request->file('file_surat_rekomendasi')->store('surat_rekomendasi', 'public') : null;

        // 6. SIMPAN DATA
        $infoKeterangan = "Pengajuan " . ($request->metode == 'interview_only' ? 'Kredensialing' : 'Uji Kompetensi');
        $infoKeterangan .= " (" . ucfirst($request->jenis_pengajuan) . ")";

        // Cari ID Lisensi lama untuk relasi (jika perpanjangan)
        $lisensiLamaId = null;
        if ($request->jenis_pengajuan == 'perpanjangan') {
            $lastLicense = PerawatLisensi::where('user_id', $user->id)
                ->where('metode', $request->metode)
                ->latest()->first();
            $lisensiLamaId = $lastLicense ? $lastLicense->id : null;
        }

        PengajuanSertifikat::create([
            'user_id'                => $user->id,
            'lisensi_lama_id'        => $lisensiLamaId,
            'status'                 => 'pending',
            'metode'                 => $request->metode,
            'jenis_pengajuan'        => $request->jenis_pengajuan,
            'link_gdrive'            => $request->link_gdrive,
            'file_dokumen_baru'      => $pathBaru,
            'file_sertifikat_lama'   => $pathLama,
            'file_surat_rekomendasi' => $pathRekom,
            'keterangan'             => $infoKeterangan
        ]);

        return redirect()->route('perawat.pengajuan.index')
            ->with('swal', ['icon' => 'success', 'title' => 'Terkirim', 'text' => 'Pengajuan berhasil dikirim.']);
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

    public function printSertifikat($id)
    {
        $user = Auth::user();
        $pengajuan = PengajuanSertifikat::where('user_id', $user->id)
            ->with(['user.perawatProfile', 'user.pendidikanTerakhir', 'lisensiLama'])
            ->findOrFail($id);

        if ($pengajuan->status != 'completed') {
            return back()->with('swal', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Sertifikat belum tersedia.']);
        }

        $lisensi = $pengajuan->lisensiLama;
        if (!$lisensi) {
            $lisensi = PerawatLisensi::where('user_id', $user->id)->where('status', 'active')->latest()->first();
            if (!$lisensi) return back()->with('swal', ['icon' => 'error', 'title' => 'Data Kosong', 'text' => 'Data Lisensi belum dibuat Admin.']);
        }

        $profile = $pengajuan->user->perawatProfile;
        $namaLengkap = $profile->nama_lengkap ?? $user->name;

        Carbon::setLocale('id');
        $tglMulai = $lisensi->tgl_mulai ?? ($lisensi->tgl_terbit ?? now());
        $carbonDate = Carbon::parse($tglMulai);
        $tglMulaiIndo = $carbonDate->translatedFormat('d F Y');
        $tglSelesaiIndo = $carbonDate->copy()->addYears(3)->translatedFormat('d F Y');
        $tglTerbitIndo = Carbon::parse($lisensi->tgl_terbit ?? now())->translatedFormat('d F Y');

        // Logic Nomor
        $tahunSurat = Carbon::parse($lisensi->tgl_terbit)->format('Y');
        $cleanNomor = preg_replace('/[^0-9\-\.]/', '', $lisensi->nomor);
        $nomorUrut = !empty($cleanNomor) ? $cleanNomor : '0';
        $lisensi->nomor_surat_full = "188/4150/418.25{$nomorUrut}/{$tahunSurat}";

        // --- WORD (Kredensialing) ---
        if ($pengajuan->metode == 'interview_only') {
            $pathTemplate = storage_path('app/templates/template_serkom.docx');
            if (!file_exists($pathTemplate)) return back()->with('swal', ['icon' => 'error', 'title' => 'Error', 'text' => 'Template tidak ditemukan.']);

            $templateProcessor = new TemplateProcessor($pathTemplate);
            $templateProcessor->setValue('nama', strtoupper($namaLengkap));
            $templateProcessor->setValue('nirp', $profile->nirp ?? '-');
            $templateProcessor->setValue('unit_kerja', strtoupper($user->unit_kerja ?? 'RSUD SLG'));
            $templateProcessor->setValue('bidang', strtoupper($lisensi->bidang ?? 'KEPERAWATAN'));
            $templateProcessor->setValue('nomor', $lisensi->nomor_surat_full);

            // KFK Logic
            $valKfk = $lisensi->kfk;
            $stringKfk = is_array($valKfk) ? implode(', ', $valKfk) : str_replace(['[', ']', '"'], '', $valKfk);
            $templateProcessor->setValue('kfk', strtoupper($stringKfk ?: $lisensi->nama));

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
            // --- PDF (Ujikom) ---
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
            $pdf = Pdf::loadView('perawat.pengajuan.sertifikat_pdf', $dataPDF)->setPaper('a4', 'landscape');
            $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $namaLengkap);
            return $pdf->download('Sertifikat_Kompetensi_' . $cleanName . '.pdf');
        }
    }
}
