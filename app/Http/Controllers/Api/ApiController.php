<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\AnggotaKelas;
use App\Models\Jadwal;
use App\Models\SesiAbsen;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ApiController extends Controller
{
    public function bukaSesi(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:jam_masuk,jam_pulang'
        ]);

        $user = JWTAuth::parseToken()->authenticate();

        $hariSekarang = 'senin'; // untuk testing
        $jamSekarang = now()->format('H:i:s');

        $jadwal = Jadwal::where('hari', $hariSekarang)
            ->where('jam_mulai', '<=', $jamSekarang)
            ->where('jam_selesai', '>=', $jamSekarang)
            ->first();

        if (!$jadwal) return response()->json(['message' => 'Tidak ada jadwal saat ini'], 404);

        if ($user->id != $jadwal->guru_id) return response()->json(['message' => 'Anda bukan guru pada jadwal ini'], 403);

        $tanggal = now()->toDateString();

        $cek = SesiAbsen::where('jadwal_id', $jadwal->id)
            ->where('tanggal', $tanggal)
            ->where('tipe', $request->tipe)
            ->first();

        if ($cek) return response()->json(['message' => 'Sesi sudah dibuka'], 400);

        $token = Str::random(40);

        $sesi = SesiAbsen::create([
            'jadwal_id' => $jadwal->id,
            'tipe' => $request->tipe,
            'tanggal' => $tanggal,
            'token_qr' => $token,
            'expired_at' => now()->addMinutes(5),
            'dibuka_oleh' => $user->id,
            'dibuka_pada' => now()
        ]);

        // generate QR image
        $qrSvg = QrCode::format('svg')->size(250)->generate($token);

        return response()->json([
            'message' => 'Sesi berhasil dibuka',
            'jadwal' => $jadwal,
            'data' => $sesi,
            'qr_image' => 'data:image/svg+xml;base64,' . base64_encode($qrSvg) // ini bisa langsung ditampilkan
        ]);
    }

    public function scan(Request $request)
    {
        $request->validate([
            'token_qr' => 'required|string'
        ]);

        $murid = JWTAuth::parseToken()->authenticate();

        if ($murid->role != 'murid') {
            return response()->json([
                'message' => 'Hanya murid yang bisa scan QR'
            ], 403);
        }

        $tanggal = now()->toDateString();

        // cek sesi berdasarkan token QR & tanggal
        $sesi = SesiAbsen::where('token_qr', $request->token_qr)
            ->where('tanggal', $tanggal)
            ->first();

        if (!$sesi) {
            return response()->json([
                'message' => 'QR tidak valid atau sesi sudah kadaluarsa'
            ], 404);
        }

        // cek token expired
        if ($sesi->expired_at < now()) {
            return response()->json([
                'message' => 'QR sudah kadaluarsa'
            ], 400);
        }

        // cek murid sudah absen atau belum
        $absen = Absensi::where('sesi_absen_id', $sesi->id)
            ->where('murid_id', $murid->id)
            ->first();

        if ($absen) {
            return response()->json([
                'message' => 'Anda sudah absen di sesi ini'
            ], 400);
        }

        // simpan absensi
        $absensi = Absensi::create([
            'sesi_absen_id' => $sesi->id,
            'murid_id' => $murid->id,
            'status' => 'hadir',
            'waktu_scan' => now(),
            'created_at' => now()
        ]);

        return response()->json([
            'message' => 'Absensi berhasil',
            'data' => $absensi
        ]);
    }

    public function absenManual(Request $request)
    {
        $request->validate([
            'sesi_id'           => 'required|exists:sesi_absen,id',
            'data'              => 'required|array',
            'data.*.murid_id'   => 'required|exists:users,id',
            'data.*.status'     => 'required|in:hadir,izin,sakit,alpha'
        ]);

        $guru = JWTAuth::parseToken()->authenticate();

        if ($guru->role !== 'guru') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $sesi = SesiAbsen::findOrFail($request->sesi_id);

        if ($guru->id !== $sesi->dibuka_oleh) {
            return response()->json(['message' => 'Bukan sesi Anda'], 403);
        }

        if (now()->greaterThan($sesi->expired_at)) {
            return response()->json(['message' => 'Sesi sudah berakhir'], 422);
        }

        foreach ($request->data as $item) {
            Absensi::updateOrCreate(
                [
                    'sesi_absen_id' => $sesi->id,
                    'murid_id'      => $item['murid_id']
                ],
                [
                    'status'     => $item['status'],
                    'waktu_scan' => now()
                ]
            );
        }

        return response()->json([
            'message' => 'Absensi berhasil disimpan / diperbarui'
        ]);
    }

    public function getMuridSesi($sesiId)
    {
        $guru = JWTAuth::parseToken()->authenticate();

        if ($guru->role !== 'guru') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $sesi = SesiAbsen::with('jadwal.kelas')->findOrFail($sesiId);

        if ($sesi->dibuka_oleh !== $guru->id) {
            return response()->json(['message' => 'Bukan sesi Anda'], 403);
        }

        $murid = AnggotaKelas::with('murid')
            ->where('kelas_id', $sesi->jadwal->kelas_id)
            ->get()
            ->map(function ($a) use ($sesi) {
                $absen = Absensi::where([
                    'sesi_absen_id' => $sesi->id,
                    'murid_id' => $a->murid->id
                ])->first();

                return [
                    'id' => $a->murid->id,
                    'name' => $a->murid->name,
                    'status' => $absen->status ?? null
                ];
            });

        return response()->json([
            'data' => $murid
        ]);
    }
}
