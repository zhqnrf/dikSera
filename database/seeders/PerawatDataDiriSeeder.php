<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Faker\Factory as Faker;

class PerawatDataDiriSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        // 1. CARI USER TARGET (perawat@gmail)
        $user = User::where('email', 'perawat@gmail')->first();

        if (!$user) {
            $this->command->error('❌ User perawat@gmail tidak ditemukan. Jalankan UserSeeder dulu!');
            return;
        }

        $user_id = $user->id;
        $this->command->info('✅ Mengisi Data Diri, Pendidikan & Riwayat untuk: ' . $user->name);

        // ==========================================
        // A. PROFILE (perawat_profiles)
        // ==========================================
        $existProfile = DB::table('perawat_profiles')->where('user_id', $user_id)->first();

        if (!$existProfile) {
            DB::table('perawat_profiles')->insert([
                'user_id'           => $user_id,
                'nik'               => $faker->nik,
                'nip'               => $faker->numerify('198####### ###### # ###'),
                'nirp'              => $faker->numerify('RP.#####'),
                'nama_lengkap'      => $user->name,
                'tempat_lahir'      => 'Kediri',
                'tanggal_lahir'     => $faker->date('Y-m-d', '-25 years'),
                'jenis_kelamin'     => 'P',
                'agama'             => 'Islam',
                'aliran_kepercayaan'=> '-',
                'status_perkawinan' => 'Menikah',
                'jabatan'           => 'Perawat Pelaksana',
                'pangkat'           => 'Penata Muda',
                'golongan'          => 'III/a',
                'alamat'            => $faker->address,
                'kota'              => 'Kediri',
                'no_hp'             => $faker->phoneNumber,
                'golongan_darah'    => 'O',
                'tinggi_badan'      => 160,
                'berat_badan'       => 55,
                'rambut'            => 'Hitam Lurus',
                'bentuk_muka'       => 'Oval',
                'warna_kulit'       => 'Sawo Matang',
                'ciri_khas'         => 'Tahi lalat di pipi',
                'cacat_tubuh'       => '-',
                'hobby'             => 'Membaca',
                'foto_3x4'          => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
            $this->command->info('-> Profile berhasil dibuat.');
        }

        // ==========================================
        // B. PENDIDIKAN (perawat_pendidikans) <-- BARU DITAMBAHKAN
        // ==========================================
        $riwayatPendidikan = [
            [
                'jenjang'        => 'D3',
                'nama_institusi' => 'Poltekkes Kemenkes Malang',
                'jurusan'        => 'Keperawatan',
                'akreditasi'     => 'A',
                'tempat'         => 'Malang',
                'tahun_masuk'    => '2015',
                'tahun_lulus'    => '2018',
            ],
            [
                'jenjang'        => 'S1',
                'nama_institusi' => 'IIK Bhakti Wiyata',
                'jurusan'        => 'S1 Keperawatan',
                'akreditasi'     => 'B',
                'tempat'         => 'Kediri',
                'tahun_masuk'    => '2019',
                'tahun_lulus'    => '2021',
            ],
            [
                'jenjang'        => 'Profesi',
                'nama_institusi' => 'IIK Bhakti Wiyata',
                'jurusan'        => 'Profesi Ners',
                'akreditasi'     => 'B',
                'tempat'         => 'Kediri',
                'tahun_masuk'    => '2021',
                'tahun_lulus'    => '2022',
            ]
        ];

        foreach ($riwayatPendidikan as $edu) {
            DB::table('perawat_pendidikans')->insert([
                'user_id'        => $user_id,
                'jenjang'        => $edu['jenjang'],
                'nama_institusi' => $edu['nama_institusi'],
                'jurusan'        => $edu['jurusan'],
                'akreditasi'     => $edu['akreditasi'],
                'tempat'         => $edu['tempat'],
                'tahun_masuk'    => $edu['tahun_masuk'],
                'tahun_lulus'    => $edu['tahun_lulus'],
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
        $this->command->info('-> Data Pendidikan berhasil diisi.');

        // ==========================================
        // C. KELUARGA (perawat_keluargas)
        // ==========================================
        $keluarga = [
            [
                'hubungan'      => 'Suami',
                'nama'          => $faker->name('male'),
                'tanggal_lahir' => $faker->date('Y-m-d', '-30 years'),
                'pekerjaan'     => 'Wiraswasta'
            ],
            [
                'hubungan'      => 'Anak',
                'nama'          => $faker->name('female'),
                'tanggal_lahir' => $faker->date('Y-m-d', '-5 years'),
                'pekerjaan'     => 'Belum Bekerja'
            ]
        ];

        foreach ($keluarga as $fam) {
            DB::table('perawat_keluargas')->insert([
                'user_id'       => $user_id,
                'hubungan'      => $fam['hubungan'],
                'nama'          => $fam['nama'],
                'tanggal_lahir' => $fam['tanggal_lahir'],
                'pekerjaan'     => $fam['pekerjaan'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
        $this->command->info('-> Data Keluarga berhasil diisi.');

        // ==========================================
        // D. ORGANISASI (perawat_organisasis)
        // ==========================================
        DB::table('perawat_organisasis')->insert([
            'user_id'           => $user_id,
            'nama_organisasi'   => 'PPNI',
            'jabatan'           => 'Anggota',
            'tempat'            => 'Kediri',
            'pemimpin'          => 'Ketua PPNI',
            'tahun_mulai'       => '2020',
            'tahun_selesai'     => '2025',
            'keterangan'        => 'Aktif',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
        $this->command->info('-> Data Organisasi berhasil diisi.');

        // ==========================================
        // E. TANDA JASA (perawat_tandajasa)
        // ==========================================
        DB::table('perawat_tandajasa')->insert([
            'user_id'           => $user_id,
            'nama_penghargaan'  => 'Perawat Teladan',
            'instansi_pemberi'  => 'RSUD SLG',
            'tahun'             => '2023',
            'nomor_sk'          => 'SK/2023/01',
            'tanggal_sk'        => '2023-08-17',
            'keterangan'        => 'Terbaik',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
        $this->command->info('-> Data Tanda Jasa berhasil diisi.');

        // ==========================================
        // F. PEKERJAAN (perawat_pekerjaans)
        // ==========================================
        DB::table('perawat_pekerjaans')->insert([
            'user_id'       => $user_id,
            'nama_instansi' => 'Klinik Sehat',
            'jabatan'       => 'Perawat',
            'tahun_mulai'   => '2018',
            'tahun_selesai' => '2019',
            'keterangan'    => 'Magang',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
        $this->command->info('-> Data Pekerjaan berhasil diisi.');

        // ==========================================
        // G. PELATIHAN (perawat_pelatihans)
        // ==========================================
        $pelatihan = [
            ['nama' => 'Pelatihan K3RS', 'penyelenggara' => 'RSUD SLG', 'durasi' => '2 Hari'],
            ['nama' => 'Manajemen Nyeri', 'penyelenggara' => 'PPNI', 'durasi' => '1 Hari']
        ];

        foreach ($pelatihan as $lat) {
            DB::table('perawat_pelatihans')->insert([
                'user_id'           => $user_id,
                'nama_pelatihan'    => $lat['nama'],
                'penyelenggara'     => $lat['penyelenggara'],
                'tempat'            => 'Kediri',
                'durasi'            => $lat['durasi'],
                'tanggal_mulai'     => $faker->date('Y-m-d', '-1 years'),
                'tanggal_selesai'   => $faker->date('Y-m-d', '-1 years'),
                'tahun'             => '2024',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
        $this->command->info('-> Data Pelatihan berhasil diisi.');

        $this->command->info('🚀 SELESAI! Semua data lengkap (termasuk Pendidikan) sudah masuk.');
    }
}
