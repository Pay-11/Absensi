<?php
$user = \App\Models\User::firstOrCreate(
    ['email' => 'siswa@test.com'],
    [
        'name' => 'Siswa Test',
        'password' => bcrypt('password'),
        'role' => 'murid'
    ]
);
echo $user->id;
