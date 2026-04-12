<?php
$data = json_decode(file_get_contents('http://127.0.0.1:8000/api/login?login=guru1@gmail.com&password=password', false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => 'login=guru1@gmail.com&password=password'
    ]
])), true);

$token = $data['token'];

$ch = curl_init('http://127.0.0.1:8000/api/penilaian-sikap');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'siswa_id' => 4,
    'sikap' => 'Sangat Baik',
    'keterangan' => 'Siswa sangat aktif dari script curl',
    'tanggal' => '2026-03-11'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
echo "RESPONSE FROM API:\n" . $response;
curl_close($ch);
