<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class AnggotaKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('anggota_kelas')->insert([
            [
                'kelas_id' => 1,
                'murid_id' => 4,
                'created_at' => now(),
            ],
            [
                'kelas_id' => 1,
                'murid_id' => 5,
                'created_at' => now(),
            ]
        ]);
    }
}
