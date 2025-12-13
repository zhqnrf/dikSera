<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PenanggungJawabUjian;
use Faker\Factory as Faker;

class PenanggungJawabUjianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');

        $jabatans = [
            'Kepala Bidang Keperawatan',
            'Ketua Komite Keperawatan',
            'Koordinator Diklat',
            'Kepala Ruangan ICU',
            'Kepala Ruangan IGD',
            'Manajer SDM & Diklat',
            'Direktur Pelayanan Medis'
        ];

        for ($i = 0; $i < 5; $i++) {
            PenanggungJawabUjian::create([
                'nama' => $faker->title . ' ' . $faker->name, 
                'no_hp' => $faker->phoneNumber,
                'jabatan' => $faker->randomElement($jabatans),
            ]);
        }
    }
}