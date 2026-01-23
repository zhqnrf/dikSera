<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalWawancara;
use App\Models\PengajuanSertifikat; // Pastikan import ini ada
use App\Services\TelegramService;
use Carbon\Carbon;

class AdminPengajuanWawancaraController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    public function approveJadwal(Request $request, $id)
    {
        $pengajuan = PengajuanSertifikat::with(['jadwalWawancara', 'user'])->findOrFail($id);
        $jadwal = $pengajuan->jadwalWawancara;

        if (!$jadwal) {
            return back()->with('error', 'Jadwal belum diajukan oleh perawat.');
        }

        // 1. Validasi Input (Tambahkan validasi pewawancara)
        $request->validate([
            'penanggung_jawab_id' => 'required|exists:penanggung_jawab_ujians,id', // [BARU]
            'tgl_wawancara'       => 'required|date',
            'jam_wawancara'       => 'required',
            'lokasi'              => 'required|string',
            'deskripsi_skill'     => 'nullable|string',
        ]);

        $waktuFix = $request->tgl_wawancara . ' ' . $request->jam_wawancara;

        // 2. Update Data Jadwal (Simpan ID Pewawancara Baru)
        $jadwal->update([
            'penanggung_jawab_id' => $request->penanggung_jawab_id, // [BARU] Simpan perubahan
            'waktu_wawancara'     => $waktuFix,
            'lokasi'              => $request->lokasi,
            'deskripsi_skill'     => $request->deskripsi_skill,
            'status'              => 'approved',
            'catatan_admin'       => null
        ]);

        $pengajuan->update(['status' => 'interview_scheduled']);

        // 3. Kirim Notifikasi Telegram (Ke pewawancara yang (baru) dipilih)
        try {
            // Refresh relasi agar mengambil data pewawancara terbaru
            $jadwal->load('pewawancara.user');

            $pewawancaraProfile = $jadwal->pewawancara;
            if ($pewawancaraProfile && $pewawancaraProfile->user) {
                $this->telegramService->notifyNewScheduleForInterviewer($pewawancaraProfile->user, $jadwal);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Gagal kirim notif telegram: " . $e->getMessage());
        }

        return back()->with('success', 'Jadwal disetujui. Pewawancara dan waktu telah diperbarui.');
    }

    public function rejectJadwal(Request $request, $id)
    {
        // Asumsi $id adalah ID Pengajuan
        $pengajuan = PengajuanSertifikat::with('jadwalWawancara')->findOrFail($id);
        $jadwal = $pengajuan->jadwalWawancara;

        if ($jadwal) {
            $jadwal->update([
                'status' => 'rejected',
                'catatan_admin' => $request->alasan
            ]);
        }

        // Status pengajuan tetap di tahap interview agar user bisa resubmit
        // Tidak perlu ubah ke 'exam_passed' jika logic view sudah handle resubmit based on jadwal status rejected

        return back()->with('success', 'Pengajuan jadwal ditolak. Perawat diminta mengajukan ulang.');
    }
}
