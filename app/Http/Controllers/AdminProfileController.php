<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class AdminProfileController extends Controller
{
    // Halaman Profil Admin (Tempat tombol Link Telegram berada)
    public function index()
    {
        $user = auth()->user();
        return view('admin.profile.index', compact('user'));
    }

    // Generate Kode untuk Admin
    public function generateCode()
    {
        $user = auth()->user();
        
        // Generate 6 digit code
        $code = strtoupper(Str::random(6));
        
        $user->telegram_verification_code = $code;
        $user->telegram_verification_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        return response()->json([
            'success' => true,
            'code' => $code,
            'bot_username' => env('TELEGRAM_BOT_USERNAME'), // Pastikan ada di .env
            'expires_at' => $user->telegram_verification_expires_at->format('H:i')
        ]);
    }

    // Putuskan Koneksi
    public function unlink()
    {
        $user = auth()->user();
        $user->telegram_chat_id = null;
        $user->save();

        return back()->with('success', 'Koneksi Telegram berhasil diputus.');
    }
    
    // Test Kirim Pesan (Opsional)
    public function testMessage()
    {
        $user = auth()->user();
        
        if (!$user->telegram_chat_id) {
            return back()->with('error', 'Telegram belum terhubung.');
        }

        $botToken = env('TELEGRAM_BOT_TOKEN');
        Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $user->telegram_chat_id,
            'text' => "🔔 Ini adalah pesan tes notifikasi untuk Admin {$user->name}."
        ]);

        return back()->with('success', 'Pesan tes berhasil dikirim ke Telegram Anda.');
    }
}