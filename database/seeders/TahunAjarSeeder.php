<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class TahunAjarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tahun_ajar')->insert([
            [
                'nama' => '2024/2025',
                'aktif' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => '2025/2026',
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
