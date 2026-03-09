<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class MapelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('mapel')->insert([
            [
                'nama_mapel' => 'Matematika',
                'kode_mapel' => 'MTK',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_mapel' => 'Bahasa Indonesia',
                'kode_mapel' => 'BIN',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
