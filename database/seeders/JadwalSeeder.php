<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jadwal')->insert([
            [
                'kelas_id' => 1,
                'mapel_id' => 1,
                'guru_id' => 2,
                'hari' => 'senin',
                'jam_mulai' => '07:00:00',
                'jam_selesai' => '08:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kelas_id' => 1,
                'mapel_id' => 1,
                'guru_id' => 2,
                'hari' => 'selasa',
                'jam_mulai' => '07:00:00',
                'jam_selesai' => '08:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kelas_id' => 1,
                'mapel_id' => 1,
                'guru_id' => 2,
                'hari' => 'rabu',
                'jam_mulai' => '07:00:00',
                'jam_selesai' => '08:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kelas_id' => 1,
                'mapel_id' => 1,
                'guru_id' => 2,
                'hari' => 'kamis',
                'jam_mulai' => '07:00:00',
                'jam_selesai' => '08:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kelas_id' => 1,
                'mapel_id' => 1,
                'guru_id' => 2,
                'hari' => 'jumat',
                'jam_mulai' => '03:00:00',
                'jam_selesai' => '22:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}