<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([

            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'nisn' => null,
                'nip' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Guru Matematika',
                'email' => 'guru1@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'guru',
                'nisn' => null,
                'nip' => '1987654321',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Guru Bahasa',
                'email' => 'guru2@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'guru',
                'nisn' => null,
                'nip' => '1987654322',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Budi',
                'email' => 'murid1@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'murid',
                'nisn' => '1234567890',
                'nip' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Siti',
                'email' => 'murid2@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'murid',
                'nisn' => '1234567891',
                'nip' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
