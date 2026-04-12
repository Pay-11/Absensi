<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AssessmentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Insert Categories
        $categories = [
            ['name' => 'Disiplin', 'description' => 'Tingkat kedisiplinan dan ketepatan waktu', 'type' => 'Student', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kerja Sama', 'description' => 'Kemampuan bekerja dalam tim/kelompok', 'type' => 'Student', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tanggung Jawab', 'description' => 'Tanggung jawab terhadap tugas yang diberikan', 'type' => 'Student', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];
        
        DB::table('assessment_categories')->insert($categories);

        // Fetch user IDs to seed relationships
        $guru = User::where('role', 'guru')->first();
        $murid = User::where('role', 'murid')->first();

        if ($guru && $murid) {
            // 2. Insert Assessments Header
            $assessmentId = DB::table('assessments')->insertGetId([
                'evaluator_id' => $guru->id,
                'evaluatee_id' => $murid->id,
                'assessment_date' => now()->toDateString(),
                'period' => 'Minggu 1 Jan 2024',
                'general_notes' => 'Siswa menunjukkan perkembangan yang positif minggu ini.',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Load the newly inserted categories
            $insertedCategories = DB::table('assessment_categories')->get();

            // 3. Insert Details
            $details = [
                ['assessment_id' => $assessmentId, 'category_id' => $insertedCategories[0]->id, 'score' => 4.5, 'created_at' => now(), 'updated_at' => now()],
                ['assessment_id' => $assessmentId, 'category_id' => $insertedCategories[1]->id, 'score' => 4.0, 'created_at' => now(), 'updated_at' => now()],
                ['assessment_id' => $assessmentId, 'category_id' => $insertedCategories[2]->id, 'score' => 5.0, 'created_at' => now(), 'updated_at' => now()],
            ];

            DB::table('assessment_details')->insert($details);
        }
    }
}
