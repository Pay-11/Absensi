<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class GuruMapelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('guru_mapel')->insert([
            [
                'guru_id' => 2,
                'mapel_id' => 1,
                'created_at' => now(),
            ],
            [
                'guru_id' => 3,
                'mapel_id' => 2,
                'created_at' => now(),
            ]
        ]);
    }
}
