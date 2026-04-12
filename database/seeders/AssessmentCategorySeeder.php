<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssessmentCategory;

class AssessmentCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Disiplin',
                'description' => 'Penilaian terhadap kedisiplinan siswa',
                'type' => 'Student',
                'is_active' => true
            ],
            [
                'name' => 'Tanggung Jawab',
                'description' => 'Penilaian terhadap tanggung jawab siswa',
                'type' => 'Student',
                'is_active' => true
            ],
            [
                'name' => 'Kerjasama',
                'description' => 'Penilaian terhadap kemampuan bekerja sama',
                'type' => 'Student',
                'is_active' => true
            ],
            [
                'name' => 'Kejujuran',
                'description' => 'Penilaian terhadap sikap jujur siswa',
                'type' => 'Student',
                'is_active' => true
            ],
            [
                'name' => 'Sopan Santun',
                'description' => 'Penilaian terhadap sikap sopan santun siswa',
                'type' => 'Student',
                'is_active' => true
            ],
        ];

        foreach ($categories as $category) {
            AssessmentCategory::create($category);
        }
    }
}