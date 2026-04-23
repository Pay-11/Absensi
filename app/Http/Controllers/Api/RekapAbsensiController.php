<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\SesiAbsen;
use App\Models\User;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class RekapAbsensiController extends Controller
{
    private function authorizeAdmin()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return in_array($user->role, ['superadmin', 'admin', 'guru']) ? $user : null;
    }

    /**
     * GET /api/absensi/rekap
     * Query: kelas_id, mapel_id, tgl_mulai, tgl_selesai
     */
    public function rekap(Request $request)
    {
        if (!$this->authorizeAdmin()) return response()->json(['message' => 'Unauthorized'], 403);

        $request->validate([
            'kelas_id'   => 'required|exists:kelas,id',
            'tgl_mulai'  => 'required|date',
            'tgl_selesai'=> 'required|date|after_or_equal:tgl_mulai',
        ]);

        $kelasId    = $request->kelas_id;
        $mapelId    = $request->mapel_id;
        $tglMulai   = $request->tgl_mulai;
        $tglSelesai = $request->tgl_selesai;

        // Siswa di kelas
        $siswaList = User::where('role', 'murid')
            ->whereHas('kelas', fn($q) => $q->where('kelas.id', $kelasId))
            ->orderBy('name')
            ->get();

        // Sesi absen
        $sesiQuery = SesiAbsen::with(['jadwal.mapel'])
            ->whereHas('jadwal', fn($q) => $q->where('kelas_id', $kelasId))
            ->whereBetween('tanggal', [$tglMulai, $tglSelesai]);

        if ($mapelId) {
            $sesiQuery->whereHas('jadwal', fn($q) => $q->where('mapel_id', $mapelId));
        }

        $sesiList = $sesiQuery->orderBy('tanggal')->get();

        // Data absensi
        $absensiData = Absensi::whereIn('sesi_absen_id', $sesiList->pluck('id'))
            ->get()
            ->groupBy('murid_id');

        $hasil = $siswaList->map(function ($siswa) use ($sesiList, $absensiData) {
            $detail = [];
            $hadir = $izin = $alpha = 0;

            foreach ($sesiList as $sesi) {
                $absensi = $absensiData->get($siswa->id)?->firstWhere('sesi_absen_id', $sesi->id);
                $status  = $absensi?->status ?? 'alpha';
                $detail[] = [
                    'sesi_id' => $sesi->id,
                    'tanggal' => $sesi->tanggal,
                    'mapel'   => $sesi->jadwal?->mapel?->nama_mapel,
                    'status'  => $status,
                    'waktu_scan' => $absensi?->waktu_scan,
                ];
                if ($status === 'hadir') $hadir++;
                elseif ($status === 'izin') $izin++;
                else $alpha++;
            }

            return [
                'murid_id' => $siswa->id,
                'name'     => $siswa->name,
                'nisn'     => $siswa->nisn,
                'hadir'    => $hadir,
                'izin'     => $izin,
                'alpha'    => $alpha,
                'total'    => $hadir + $izin + $alpha,
                'detail'   => $detail,
            ];
        });

        return response()->json([
            'message'    => 'Rekap absensi berhasil diambil',
            'periode'    => ['dari' => $tglMulai, 'sampai' => $tglSelesai],
            'total_sesi' => $sesiList->count(),
            'data'       => $hasil,
        ]);
    }

    /**
     * GET /api/absensi/sesi/{id}
     * Detail satu sesi
     */
    public function detailSesi($id)
    {
        if (!$this->authorizeAdmin()) return response()->json(['message' => 'Unauthorized'], 403);

        $sesi    = SesiAbsen::with(['jadwal.kelas','jadwal.mapel','jadwal.guru'])->findOrFail($id);
        $absensi = Absensi::with('murid')
            ->where('sesi_absen_id', $id)
            ->get()
            ->map(fn($a) => [
                'murid_id'   => $a->murid_id,
                'name'       => $a->murid?->name,
                'nisn'       => $a->murid?->nisn,
                'status'     => $a->status,
                'waktu_scan' => $a->waktu_scan,
            ]);

        return response()->json([
            'message' => 'Detail sesi berhasil diambil',
            'sesi'    => [
                'id'      => $sesi->id,
                'tanggal' => $sesi->tanggal,
                'kelas'   => $sesi->jadwal?->kelas?->nama_kelas,
                'mapel'   => $sesi->jadwal?->mapel?->nama_mapel,
                'guru'    => $sesi->jadwal?->guru?->name,
            ],
            'data'    => $absensi,
        ]);
    }
}
