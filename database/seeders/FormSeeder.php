<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Form;
use App\Models\User;
use App\Models\PenanggungJawabUjian;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FormSeeder extends Seeder
{
    public function run()
    {
        $pj = PenanggungJawabUjian::inRandomOrder()->first();
        $pjId = $pj ? $pj->id : null;

        Form::create([
            'judul' => 'Ujian Kompetensi Dasar Perawat 2024',
            'slug' => Str::slug('Ujian Kompetensi Dasar Perawat 2024') . '-' . Str::random(5),
            'deskripsi' => 'Ujian ini wajib diikuti oleh seluruh perawat untuk pemetaan kompetensi dasar tahunan.',
            'penanggung_jawab_id' => $pjId,
            'waktu_mulai' => Carbon::now()->addDays(1)->setHour(8)->setMinute(0),
            'waktu_selesai' => Carbon::now()->addDays(1)->setHour(12)->setMinute(0),
            'status' => 'publish',
            'target_peserta' => 'semua',
        ]);

        $formKhusus = Form::create([
            'judul' => 'Pemutakhiran Data & Ujian STR Expired',
            'slug' => Str::slug('Pemutakhiran Data STR Expired') . '-' . Str::random(5),
            'deskripsi' => 'Formulir khusus bagi perawat yang dokumen STR/SIP-nya mendekati masa kedaluwarsa.',
            'penanggung_jawab_id' => $pjId,
            'waktu_mulai' => Carbon::now()->addDays(3)->setHour(9)->setMinute(0),
            'waktu_selesai' => Carbon::now()->addDays(3)->setHour(15)->setMinute(0),
            'status' => 'publish',
            'target_peserta' => 'khusus',
        ]);

        $allPerawat = User::where('role', 'perawat')->get();

        $expiredUsers = $allPerawat->filter(function ($user) {
            return count($user->dokumen_warning) > 0;
        });

        if ($expiredUsers->isEmpty()) {
            $expiredUsers = $allPerawat->random(min(2, $allPerawat->count()));
        }

        $formKhusus->participants()->attach($expiredUsers->pluck('id'));
    }
}
