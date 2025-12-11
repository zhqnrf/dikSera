<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 1. Admin (Otomatis Active)
        User::updateOrCreate(
            ['email' => 'admin@gmail'],
            [
                'name'             => 'Admin DIKSERA',
                'password'         => Hash::make('password'),
                'role'             => 'admin',
                'telegram_chat_id' => null,
                'status_akun'      => 'active', // <--- Tambahkan ini
            ]
        );

        // 2. Contoh Perawat (Otomatis Active)
        User::updateOrCreate(
            ['email' => 'perawat@gmail'],
            [
                'name'             => 'Perawat Contoh',
                'password'         => Hash::make('password'),
                'role'             => 'perawat',
                'telegram_chat_id' => null,
                'status_akun'      => 'active', // <--- Tambahkan ini agar bisa langsung login
            ]
        );

        // 3. Contoh Pewawancara (Otomatis Active)
        User::updateOrCreate(
            ['email' => 'pewawancara@gmail'],
            [
                'name'             => 'Pewawancara Contoh',
                'password'         => Hash::make('password'),
                'role'             => 'pewawancara',
                'telegram_chat_id' => null,
                'status_akun'      => 'active', // <--- Tambahkan ini
            ]
        );
    }
}
