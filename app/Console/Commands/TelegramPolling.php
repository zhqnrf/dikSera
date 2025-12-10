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

        $this->info("🤖 Bot sedang berjalan... Menunggu pesan masuk.");

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
                            $text = strtoupper(trim($update['message']['text']));

                            $this->comment("📩 Pesan diterima: {$text} dari ID: {$chatId}");

                            // 1. Cek Kode Verifikasi (Untuk Login/Link Akun)
                            $user = User::where('telegram_verification_code', $text)
                                ->where('telegram_verification_expires_at', '>', Carbon::now())
                                ->first();

                            if ($user) {
                                // Simpan Chat ID ke Database
                                $user->telegram_chat_id = $chatId;
                                $user->telegram_verification_code = null;
                                $user->telegram_verification_expires_at = null;
                                $user->save();

                                // Pesan Berbeda tergantung Role
                                $msgRole = $user->role === 'admin' ? 'Admin' : 'Perawat';
                                
                                Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                                    'chat_id' => $chatId,
                                    'text' => "✅ *Berhasil Terhubung!*\n\nHalo {$user->name} ({$msgRole}), akun Anda telah berhasil ditautkan. Anda sekarang akan menerima notifikasi sistem di sini.",
                                    'parse_mode' => 'Markdown'
                                ]);

                                $this->info("✓ User {$user->name} ({$user->role}) connected!");
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->error("Connection Error: " . $e->getMessage());
                sleep(5); // Tunggu 5 detik jika koneksi putus
            }

            // Hindari CPU spike
            usleep(500000); // 0.5 detik
        }
    }
}