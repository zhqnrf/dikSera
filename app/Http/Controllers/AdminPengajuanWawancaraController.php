<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalWawancara;
use App\Services\TelegramService;

class AdminPengajuanWawancaraController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    public function approveJadwal($id)
    {
        // PERBAIKAN 1: Ganti 'penanggungJawab' jadi 'pewawancara' di eager loading
        $jadwal = JadwalWawancara::with(['pewawancara.user', 'pengajuan.user'])->findOrFail($id);

        if (!$jadwal->penanggung_jawab_id) {
            return back()->with('error', 'Pewawancara belum ditentukan untuk jadwal ini.');
        }

        $jadwal->update(['status' => 'approved']);

        // Kirim notifikasi ke Pewawancara
        try {
            // PERBAIKAN 2: Akses relasi menggunakan nama fungsi 'pewawancara'
            $pewawancaraProfile = $jadwal->pewawancara;

            if ($pewawancaraProfile && $pewawancaraProfile->user) {
                $userPewawancara = $pewawancaraProfile->user;

                $this->telegramService->notifyNewScheduleForInterviewer($userPewawancara, $jadwal);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Gagal kirim notif telegram: " . $e->getMessage());
        }
        // -----------------------------------------------------------

        return back()->with('success', 'Jadwal disetujui. Notifikasi telah dikirim ke Pewawancara.');
    }

    public function rejectJadwal(Request $request, $id)
    {
        $jadwal = JadwalWawancara::findOrFail($id);

        $jadwal->update([
            'status' => 'rejected',
            'catatan_admin' => $request->alasan
        ]);

        $jadwal->pengajuan->update(['status' => 'exam_passed']);

        return back()->with('success', 'Pengajuan jadwal ditolak/dikembalikan ke peserta.');
    }
}
