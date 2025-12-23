<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Carbon\Carbon;

class TelegramPolling extends Command
{
    protected $signature = 'telegram:polling';
    protected $description = 'Listen to Telegram updates via polling';

    public function handle()
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $offset = 0;

        $this->info("🤖 Bot Sistem Kepegawaian RSUD SLG berjalan...");

        while (true) {
            try {
                $response = Http::timeout(35)->get("https://api.telegram.org/bot{$botToken}/getUpdates", [
                    'offset' => $offset,
                    'timeout' => 30
                ]);

                if ($response->successful()) {
                    $updates = $response->json()['result'] ?? [];

                    foreach ($updates as $update) {
                        $offset = $update['update_id'] + 1;

                        if (isset($update['message']['text'])) {
                            $chatId = $update['message']['chat']['id'];
                            $text = strtoupper(trim($update['message']['text'])); // Convert ke Uppercase biar aman
                            $firstName = $update['message']['from']['first_name'] ?? 'User';

                            $this->comment("📩 Pesan: {$text} | ID: {$chatId}");

                            // ----------------------------------------------------
                            // 1. LOGIKA UTAMA: Handle Command /START
                            // ----------------------------------------------------
                            if ($text === '/START') {
                                $welcomeMsg = "👋 <b>Selamat Datang di Bot Notifikasi Kepegawaian</b>\n";
                                $welcomeMsg .= "<b>RSUD Simpang Lima Gumul Kediri</b>\n\n";
                                $welcomeMsg .= "Silahkan kirimkan <b>Kode Verifikasi</b> yang Anda dapatkan dari aplikasi web.\n";
                                $welcomeMsg .= "⚠️ <i>Pastikan kode tersebut belum kadaluarsa.</i>";

                                Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                                    'chat_id' => $chatId,
                                    'text' => $welcomeMsg,
                                    'parse_mode' => 'HTML'
                                ]);

                                continue; // Lanjut ke pesan berikutnya, jangan cek DB
                            }

                            // ----------------------------------------------------
                            // 2. LOGIKA UTAMA: Cek Kode Verifikasi
                            // ----------------------------------------------------
                            $user = User::where('telegram_verification_code', $text)
                                ->where('telegram_verification_expires_at', '>', Carbon::now())
                                ->first();

                            if ($user) {
                                // Simpan Chat ID ke Database
                                $user->telegram_chat_id = $chatId;
                                $user->telegram_verification_code = null;
                                $user->telegram_verification_expires_at = null;
                                $user->save();

                                // 1. Tentukan Label Role (PHP 7.4 Compatible)
                                if ($user->role === 'admin') {
                                    $msgRole = 'Administrator';
                                } elseif ($user->role === 'pewawancara') {
                                    $msgRole = 'Pewawancara';
                                } else {
                                    $msgRole = 'Staf Perawat';
                                }

                                // 2. Susun Header Pesan
                                $successMsg = "✅ <b>AKUN TERHUBUNG!</b>\n\n";
                                $successMsg .= "Yth. <b>{$user->name}</b> ({$msgRole}),\n";
                                $successMsg .= "Akun Telegram Anda telah berhasil ditautkan dengan sistem DIKSERA RSUD SLG.\n\n";

                                // 3. Pesan Tambahan Sesuai Role
                                if ($user->role === 'pewawancara') {
                                    $successMsg .= "Anda akan menerima notifikasi saat ada jadwal wawancara baru.";
                                } else {
                                    $successMsg .= "Anda akan menerima notifikasi resmi terkait masa berlaku dokumen melalui chat ini.";
                                }
                                // Kirim Pesan Sukses ke User
                                Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                                    'chat_id' => $chatId,
                                    'text' => $successMsg,
                                    'parse_mode' => 'HTML'
                                ]);

                                $this->info("✓ User {$user->name} connected!");
                            } else {
                                // Balas jika kode salah 
                                Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                                    'chat_id' => $chatId,
                                    'text' => "❌ Kode tidak valid atau sudah kadaluarsa. Silahkan generate ulang di website.",
                                ]);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->error("Connection Error: " . $e->getMessage());
                sleep(5);
            }

            usleep(500000);
        }
    }
}
