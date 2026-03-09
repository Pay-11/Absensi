<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Jadwal;

class JadwalTestingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hari = 'senin';
        $guruId = 2; // ganti sesuai id guru di database

        // jadwal pertama (jam masuk)
        Jadwal::create([
            'kelas_id' => 1,
            'mapel_id' => 1,
            'guru_id' => $guruId,
            'hari' => $hari,
            'jam_mulai' => '20:30:00',
            'jam_selesai' => '21:00:00',
        ]);

        // jadwal terakhir (jam pulang)
        Jadwal::create([
            'kelas_id' => 1,
            'mapel_id' => 2,
            'guru_id' => $guruId,
            'hari' => $hari,
            'jam_mulai' => '21:00:00',
            'jam_selesai' => '22:50:00',
        ]);
    }
}
