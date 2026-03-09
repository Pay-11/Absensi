<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jadwal')->insert([
            [
                'kelas_id' => 1,
                'mapel_id' => 1,
                'guru_id' => 2,
                'hari' => 'senin',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '12:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kelas_id' => 1,
                'mapel_id' => 2,
                'guru_id' => 3,
                'hari' => 'selasa',
                'jam_mulai' => '10:00:00',
                'jam_selesai' => '15:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
