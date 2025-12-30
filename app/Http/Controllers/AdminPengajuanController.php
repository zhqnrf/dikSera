<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanSertifikat;
use App\Models\PerawatLisensi;

class AdminPengajuanController extends Controller
{
    public function index(Request $request)
    {
        $listSertifikat = PerawatLisensi::select('nama')->distinct()->pluck('nama');

        $query = PengajuanSertifikat::with(['user', 'lisensiLama', 'jadwalWawancara', 'user.examResult']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
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

        if ($request->filled('ujian')) {
            if ($request->ujian == 'sudah') {
                $query->whereHas('user.examResult');
            } elseif ($request->ujian == 'belum') {
                $query->whereDoesntHave('user.examResult');
            }
        }
        $pengajuan = $query->latest()->paginate(10);

        return view('admin.pengajuan.index', compact('pengajuan', 'listSertifikat'));
    }

    public function approve(Request $request, $id)
{
    $pengajuan = PengajuanSertifikat::findOrFail($id);

    if (!$pengajuan->metode) {
         $pengajuan->metode = 'pg_only';
    }

    if ($pengajuan->metode == 'interview_only') {
        $pengajuan->update([
            'status' => 'exam_passed',
        ]);
        $msg = "Pengajuan disetujui. Metode: Hanya Wawancara. Peserta dapat langsung mengajukan jadwal.";
    } else {
        $pengajuan->update([
            'status' => 'method_selected',
        ]);
        $msg = "Pengajuan disetujui. Perawat dapat segera ujian.";
    }

    return back()->with('success', $msg);
}

    public function reject($id)
    {
        $pengajuan = PengajuanSertifikat::findOrFail($id);
        $pengajuan->update(['status' => 'rejected']);

        return back()->with('success', 'Pengajuan ditolak.');
    }

    // --- PERBAIKAN 1: Single Approve Score ---
    public function approveExamScore($id)
    {
        $pengajuan = PengajuanSertifikat::findOrFail($id);
        $examResult = $pengajuan->user->examResult;

        // [LOGIKA BARU] Cek Exam Result hanya jika BUKAN interview_only
        if ($pengajuan->metode != 'interview_only') {
            if (!$examResult) {
                return back()->with('error', 'Peserta belum mengerjakan ujian! Tidak ada nilai untuk disetujui.');
            }
            // Cek jika exam terakhir masih remidi
            if ($examResult->remidi ?? ($examResult->total_nilai < 75)) {
                return back()->with('error', 'Nilai peserta masih remidi (<75). Tidak bisa di-approve sebelum lulus.');
            }
            // Update status lulus hanya jika ada exam result dan sudah tidak remidi
            $examResult->update(['lulus' => 1]);
        }

        if ($pengajuan->metode == 'pg_only') {
            // ... (Kode pg_only tetap sama) ...
            $pengajuan->update(['status' => 'completed']);
            if ($pengajuan->lisensiLama) {
                $pengajuan->lisensiLama->update([
                    'tgl_terbit'  => now(),
                    'tgl_expired' => now()->addYears(3)
                ]);
            }
            return back()->with('success', "Nilai (Skor: {$examResult->total_nilai}) disetujui & Status Ujian diubah LULUS. Proses selesai.");

        } else if ($pengajuan->metode == 'interview_only') {
            // [KHUSUS INTERVIEW ONLY] Langsung finalize tanpa butuh ExamResult
            $pengajuan->update(['status' => 'completed']);
            $user = $pengajuan->user;
            $lisensi = $pengajuan->lisensiLama;
            $newTerbit = $lisensi && $lisensi->tgl_terbit ? $lisensi->tgl_terbit : now();
            $newExpired = \Carbon\Carbon::parse($newTerbit)->addYears(3);

            // Update lisensi user
            $user->lisensis()->update([
                'tgl_terbit' => $newTerbit,
                'tgl_expired' => $newExpired
            ]);

            return back()->with('success', "Pengajuan wawancara disetujui & selesai. Lisensi diperpanjang.");

        } else {
            // [PG + Interview]
            $pengajuan->update(['status' => 'exam_passed']);
            return back()->with('success', "Nilai (Skor: {$examResult->total_nilai}) disetujui & Status Ujian diubah LULUS. Menunggu jadwal wawancara.");
        }
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

    public function completeProcess($id)
    {
        $pengajuan = PengajuanSertifikat::findOrFail($id);

        $pengajuan->update(['status' => 'completed']);

        if ($pengajuan->metode == 'interview_only') {
            $user = $pengajuan->user;
            $lisensi = $pengajuan->lisensiLama;
            $newTerbit = $lisensi && $lisensi->tgl_terbit ? $lisensi->tgl_terbit : now();
            $newExpired = \Carbon\Carbon::parse($newTerbit)->addYears(3);
            $user->lisensis()->update([
                'tgl_terbit' => $newTerbit,
                'tgl_expired' => $newExpired
            ]);
        } else if ($pengajuan->lisensiLama) {
            $pengajuan->lisensiLama->update([
                'tgl_terbit'  => now(),
                'tgl_expired' => now()->addYears(3)
            ]);
        }
        return back()->with('success', 'Proses perpanjangan selesai sepenuhnya & Lisensi diperbarui.');
    }

   public function bulkApprove(Request $request)
{
    // ... validasi ...
    $ids = $request->ids;
    $pengajuans = PengajuanSertifikat::whereIn('id', $ids)->where('status', 'pending')->get();
    $count = 0;

    foreach ($pengajuans as $pengajuan) {
        if (!$pengajuan->metode) {
             $pengajuan->metode = 'pg_only';
        }

        // --- LOGIKA BARU ---
        if ($pengajuan->metode == 'interview_only') {
            $pengajuan->update(['status' => 'exam_passed']);
        } else {
            $pengajuan->update(['status' => 'method_selected']);
        }
        $count++;
    }

    return back()->with('success', "Berhasil menyetujui $count pengajuan terpilih.");
}

    // --- PERBAIKAN 2: Bulk Approve Score ---
    public function bulkApproveScore(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pengajuan_sertifikats,id'
        ]);

        $ids = $request->ids;

        $pengajuans = PengajuanSertifikat::with('user.examResult', 'lisensiLama')
                        ->whereIn('id', $ids)
                        ->where('status', 'method_selected')
                        ->get();

        $count = 0;
        $skipped = 0;

        foreach ($pengajuans as $pengajuan) {
            // [LOGIKA BARU]
            // Kondisi A: Punya Exam Result (Untuk PG Only atau PG+Interview)
            // Kondisi B: Metode Interview Only (Tidak butuh Exam Result)
            $hasExam = ($pengajuan->user && $pengajuan->user->examResult);
            $isInterviewOnly = ($pengajuan->metode == 'interview_only');

            if ($hasExam || $isInterviewOnly) {

                // Jika punya exam result, update lulus
                if ($hasExam) {
                    $pengajuan->user->examResult->update(['lulus' => 1]);
                }

                if ($pengajuan->metode == 'pg_only') {
                     // ... (Kode pg_only tetap sama) ...
                    $pengajuan->update(['status' => 'completed']);
                    if ($pengajuan->lisensiLama) {
                        $pengajuan->lisensiLama->update([
                            'tgl_terbit'  => now(),
                            'tgl_expired' => now()->addYears(3)
                        ]);
                    }
                } else if ($pengajuan->metode == 'interview_only') {
                    // ... (Kode interview_only tetap sama) ...
                    $pengajuan->update(['status' => 'completed']);
                    $user = $pengajuan->user;
                    $lisensi = $pengajuan->lisensiLama;
                    $newTerbit = $lisensi && $lisensi->tgl_terbit ? $lisensi->tgl_terbit : now();
                    $newExpired = \Carbon\Carbon::parse($newTerbit)->addYears(3);

                    // Gunakan whereId atau relasi yang spesifik agar tidak mengupdate semua lisensi jika user punya banyak
                    // Asumsi: lisensis() me-refer ke relasi yang benar atau gunakan query update spesifik
                    if($lisensi) {
                         $lisensi->update([
                            'tgl_terbit' => $newTerbit,
                            'tgl_expired' => $newExpired
                        ]);
                    }
                } else {
                    $pengajuan->update(['status' => 'exam_passed']);
                }
                $count++;
            } else {
                $skipped++;
            }
        }

        $msg = "Berhasil memproses $count peserta.";
        if ($skipped > 0) {
            $msg .= " ($skipped peserta dilewati karena belum mengerjakan ujian).";
        }

        return back()->with('success', $msg);
    }
    public function bulkApproveInterview(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pengajuan_sertifikats,id'
        ]);

        $ids = $request->ids;

        // Ambil pengajuan yang punya jadwal pending
        $pengajuans = PengajuanSertifikat::with('jadwalWawancara')
                        ->whereIn('id', $ids)
                        ->where('status', 'interview_scheduled')
                        ->get();

        $count = 0;
        $skipped = 0;

        foreach ($pengajuans as $pengajuan) {
            $jadwal = $pengajuan->jadwalWawancara;

            // Cek jika ada jadwal dan statusnya masih pending
            if ($jadwal && $jadwal->status == 'pending') {
                $jadwal->update(['status' => 'approved']);
                $count++;
            } else {
                $skipped++;
            }
        }

        $msg = "Berhasil menyetujui $count jadwal wawancara.";
        if ($skipped > 0) {
            $msg .= " ($skipped data dilewati karena jadwal tidak ditemukan atau sudah diproses).";
        }

        return back()->with('success', $msg);
    }

    public function destroy($id)
    {
        $pengajuan = PengajuanSertifikat::findOrFail($id);

        // Hapus data
        $pengajuan->delete();

        return back()->with('success', 'Data pengajuan berhasil dihapus permanen.');
    }

    /**
     * Menghapus banyak data sekaligus (Bulk Delete).
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pengajuan_sertifikats,id'
        ]);

        $ids = $request->ids;

        // Hapus data berdasarkan array ID
        $count = PengajuanSertifikat::whereIn('id', $ids)->delete();

        return back()->with('success', "Berhasil menghapus $count data pengajuan secara permanen.");
    }
}
