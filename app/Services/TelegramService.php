<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class TelegramService
{
    protected $botToken;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
    }

    private function executeSendMessage($chatId, $message)
    {
        if (empty($this->botToken) || empty($chatId)) {
            return false;
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Telegram Error (ID: $chatId): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Notifikasi untuk ADMIN 
     */
    public function notifySertifikatExpiring($nurseUser, $sertifikat, $tipeSertifikat, $daysLeft)
    {
        $admins = User::where('role', 'admin')
            ->whereNotNull('telegram_chat_id')
            ->get();

        if ($admins->isEmpty()) return false;

        $status = $this->getStatusText($daysLeft);
        $tglExp = date('d F Y', strtotime($sertifikat->tgl_expired ?? $sertifikat->tgl_berakhir ?? now()));

        // HEADER FORMAL
        $message = "<b>🏛️ LAPORAN STATUS DOKUMEN PEGAWAI</b>\n";
        $message .= "<b>RSUD SIMPANG LIMA GUMUL KEDIRI</b>\n";
        $message .= "---------------------------------------\n\n";

        $message .= "Yth. Administrator,\n";
        $message .= "Berikut dilaporkan data dokumen perawat yang memerlukan perhatian:\n\n";

        $message .= "👤 <b>IDENTITAS PERAWAT</b>\n";
        $message .= "Nama : {$nurseUser->name}\n";
        $message .= "NIK  : " . ($nurseUser->profile->nik ?? '-') . "\n";
        $message .= "Nomor Telepon : " . ($nurseUser->profile->no_hp ?? '-') . "\n\n";

        $message .= "📄 <b>DETAIL DOKUMEN</b>\n";
        $message .= "Jenis : {$tipeSertifikat}\n";
        $message .= "Nomor : {$sertifikat->nomor}\n";
        $message .= "Berlaku s.d : <b>{$tglExp}</b>\n";
        $message .= "Status : {$status}\n\n";

        $message .= "<i>Mohon untuk menindaklanjuti informasi ini kepada pegawai yang bersangkutan. Terima kasih.</i>";

        $successCount = 0;
        foreach ($admins as $admin) {
            if ($this->executeSendMessage($admin->telegram_chat_id, $message)) {
                $successCount++;
            }
        }

        return $successCount > 0;
    }

    /**
     * Notifikasi untuk USER/PERAWAT 
     */
    public function notifySertifikatExpiringToUser($chatId, $sertifikat, $tipeSertifikat, $daysLeft)
    {
        $status = $this->getStatusText($daysLeft);
        $tglExp = date('d F Y', strtotime($sertifikat->tgl_expired ?? $sertifikat->tgl_berakhir ?? now()));

        // HEADER FORMAL
        $message = "<b>🏛️ SISTEM INFORMASI KEPEGAWAIAN</b>\n";
        $message .= "<b>RSUD SIMPANG LIMA GUMUL KEDIRI</b>\n";
        $message .= "---------------------------------------\n\n";

        $message .= "<b>PEMBERITAHUAN MASA BERLAKU DOKUMEN</b>\n\n";

        $message .= "Yth. Perawat,\n";
        $message .= "Diinformasikan bahwa dokumen kepegawaian Anda dengan rincian:\n\n";

        $message .= "🔹 <b>Jenis Dokumen:</b> {$tipeSertifikat}\n";
        $message .= "🔹 <b>Nomor:</b> {$sertifikat->nomor}\n";
        $message .= "🔹 <b>Tanggal Kadaluarsa:</b> {$tglExp}\n";
        $message .= "🔹 <b>Status Saat Ini:</b> {$status}\n\n";

        if ($daysLeft <= 0) {
            $message .= "⛔ <b>TINDAKAN DIPERLUKAN:</b>\n";
            $message .= "Dokumen Anda telah habis masa berlakunya. Mohon segera lakukan pembaruan data ke bagian administrasi.\n";
        } else {
            $message .= "📝 <b>TINDAKAN DIPERLUKAN:</b>\n";
            $message .= "Mohon mempersiapkan proses perpanjangan sebelum tanggal yang ditentukan agar tidak mengganggu proses administrasi.\n";
        }

        $message .= "\n<i>Pesan ini dikirim otomatis oleh sistem. Jangan membalas pesan ini.</i>";

        return $this->executeSendMessage($chatId, $message);
    }

    private function getStatusText($daysLeft)
    {
        if ($daysLeft < 0) return "🔴 <b>SUDAH KADALUARSA</b>";
        if ($daysLeft == 0) return "🔴 <b>HABIS HARI INI</b>";
        if ($daysLeft <= 30) return "🟠 <b>AKAN HABIS</b> (Sisa {$daysLeft} hari)";
        return "⚠️ <b>PERLU PERHATIAN</b> (Sisa {$daysLeft} hari)";
    }

    public function sendMessage($message)
    {
        return false;
    }

    public function sendVerificationCode($chatId, $code)
    {
        $message = "🔐 <b>Kode Verifikasi Telegram</b>\n\n";
        $message .= "Kode Anda: <code>{$code}</code>\n\n";
        $message .= "Masukkan kode ini di aplikasi untuk menghubungkan akun.\n";
        $message .= "Kode berlaku 15 menit.";

        return $this->executeSendMessage($chatId, $message);
    }

    /**
     * Notifikasi ketika user mengajukan dokumen Seumur Hidup (Lifetime)
     */
    public function notifyLifetimeRequested($nurseUser, $dokumen)
    {
        $admins = User::where('role', 'admin')
            ->whereNotNull('telegram_chat_id')
            ->get();

        if ($admins->isEmpty()) return false;

        $message = "<b>📣 PEMBERITAHUAN PENGAJUAN LIFETIME DOKUMEN</b>\n";
        $message .= "Perawat: <b>" . $nurseUser->name . "</b>\n";
        $message .= "Jenis: " . ($dokumen->jenis ?? '-') . "\n";
        $message .= "Nama Dokumen: " . ($dokumen->nama ?? '-') . "\n";
        $message .= "Nomor: " . ($dokumen->nomor ?? '-') . "\n";
        $message .= "Waktu Pengajuan: " . date('d F Y H:i') . "\n\n";
        $message .= "Silakan buka panel admin untuk melakukan verifikasi: " . url('/admin/perawat/' . $nurseUser->id) . "\n";

        $sent = 0;
        foreach ($admins as $admin) {
            if ($this->executeSendMessage($admin->telegram_chat_id, $message)) {
                $sent++;
            }
        }

        return $sent > 0;
    }

    /**
     * Notifikasi untuk PEWAWANCARA saat ada jadwal baru
     */
    public function notifyNewScheduleForInterviewer($pewawancaraUser, $jadwal)
    {
        if (!$pewawancaraUser->telegram_chat_id) return false;

        $tglWawancara = date('d F Y', strtotime($jadwal->waktu_wawancara));
        $jamWawancara = date('H:i', strtotime($jadwal->waktu_wawancara));
        $namaPeserta = $jadwal->pengajuan->user->name;

        $message = "<b>📅 JADWAL WAWANCARA BARU</b>\n";
        $message .= "---------------------------------------\n\n";
        $message .= "Halo {$pewawancaraUser->name},\n";
        $message .= "Anda telah dijadwalkan untuk melakukan wawancara dengan detail berikut:\n\n";

        $message .= "👤 <b>Peserta:</b> {$namaPeserta}\n";
        $message .= "📆 <b>Tanggal:</b> {$tglWawancara}\n";
        $message .= "⏰ <b>Jam:</b> {$jamWawancara} WIB\n";
        $message .= "📍 <b>Tempat:</b> " . ($jadwal->lokasi) . "\n\n";

        $message .= "Silakan login ke dashboard untuk melakukan penilaian.\n";
        $message .= "<i>Selamat bertugas!</i>";

        return $this->executeSendMessage($pewawancaraUser->telegram_chat_id, $message);
    }
}
