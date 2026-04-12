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
use App\Models\TahunAjar;
use App\Models\AssessmentCategory;
use App\Models\Assessment;
use App\Models\AssessmentDetail;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function bukaSesi(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $hariSekarang = strtolower(now()->locale('id')->dayName);
        $jamSekarang = now()->format('H:i:s');
        $tanggal = now()->toDateString();

        $jadwal = Jadwal::where('hari', $hariSekarang)
            ->where('jam_mulai', '<=', $jamSekarang)
            ->where('jam_selesai', '>=', $jamSekarang)
            ->first();

        if (!$jadwal)
            return response()->json(['message' => 'Tidak ada jadwal saat ini'], 404);
        if ($user->id != $jadwal->guru_id)
            return response()->json(['message' => 'Anda bukan guru pada jadwal ini'], 403);

        // Langsung cek, jadwal ini hari ini udah buka sesi belum? (Tanpa peduli ini absen masuk/pulang)
        $cek = SesiAbsen::where('jadwal_id', $jadwal->id)
            ->where('tanggal', $tanggal)
            ->first();

        if ($cek)
            return response()->json(['message' => 'Sesi sudah dibuka untuk jadwal ini'], 400);

        $token = Str::random(40);
        $sesi = SesiAbsen::create([
            'jadwal_id' => $jadwal->id,
            // 'tipe' dihapus
            'tanggal' => $tanggal,
            'token_qr' => $token,
            'dibuka_oleh' => $user->id,
            'dibuka_pada' => now()
        ]);

        $qrSvg = QrCode::format('svg')->size(250)->generate($token);

        return response()->json([
            'message' => 'Sesi berhasil dibuka',
            'jadwal' => $jadwal,
            'data' => $sesi,
            'qr_image' => 'data:image/svg+xml;base64,' . base64_encode($qrSvg)
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
        $sesi = SesiAbsen::with('jadwal')
            ->where('token_qr', $request->token_qr)
            ->where('tanggal', $tanggal)
            ->first();

        if (!$sesi) {
            return response()->json([
                'message' => 'QR tidak valid atau sesi sudah kadaluarsa'
            ], 404);
        }

        // cek token expired berdasarkan jam selesai jadwal
        if (now()->format('H:i:s') > $sesi->jadwal->jam_selesai) {
            return response()->json([
                'message' => 'Sesi absen sudah berakhir (melewati jam selesai)'
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
            'sesi_id' => 'required|exists:sesi_absen,id',
            'data' => 'required|array',
            'data.*.murid_id' => 'required|exists:users,id',
            'data.*.status' => 'required|in:hadir,izin,sakit,alpha'
        ]);

        $guru = JWTAuth::parseToken()->authenticate();

        if ($guru->role !== 'guru') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $sesi = SesiAbsen::with('jadwal')->findOrFail($request->sesi_id);

        if ($guru->id !== $sesi->dibuka_oleh) {
            return response()->json(['message' => 'Bukan sesi Anda'], 403);
        }

        if (now()->format('H:i:s') > $sesi->jadwal->jam_selesai) {
            return response()->json(['message' => 'Sesi sudah berakhir'], 422);
        }

        foreach ($request->data as $item) {
            Absensi::updateOrCreate(
            [
                'sesi_absen_id' => $sesi->id,
                'murid_id' => $item['murid_id']
            ],
            [
                'status' => $item['status'],
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

    public function tahunAjar()
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $data = TahunAjar::orderBy('nama', 'desc')->get([
            'id',
            'nama',
            'aktif'
        ]);

        return response()->json([
            'data' => $data
        ]);
    }

    public function kelas(Request $request)
    {
        $request->validate([
            'tahun_ajar_id' => 'required|exists:tahun_ajar,id'
        ]);

        $guru = JWTAuth::parseToken()->authenticate();

        if ($guru->role !== 'guru') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $kelas = Jadwal::with('kelas')
            ->where('guru_id', $guru->id)
            ->whereHas('kelas', function ($q) use ($request) {
            $q->where('tahun_ajar_id', $request->tahun_ajar_id);
        })
            ->get()
            ->pluck('kelas')
            ->unique('id')
            ->values();

        return response()->json([
            'data' => $kelas->map(function ($k) {
            return [
                    'id' => $k->id,
                    'nama_kelas' => $k->nama_kelas
                ];
        })
        ]);
    }

    public function muridKelas(Request $request, $kelasId)
    {
        $guru = JWTAuth::parseToken()->authenticate();

        if ($guru->role !== 'guru') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $period = $request->query('period');

        $murid = AnggotaKelas::with('murid')
            ->where('kelas_id', $kelasId)
            ->get()
            ->map(function ($a) use ($guru, $period) {

            $sudahDinilai = Assessment::where('evaluatee_id', $a->murid->id)
                ->where('evaluator_id', $guru->id)
                ->where('period', $period)
                ->exists();

            return [
            'id' => $a->murid->id,
            'name' => $a->murid->name,
            'isEvaluated' => $sudahDinilai
            ];
        });

        return response()->json([
            'data' => $murid
        ]);
    }

    public function assessmentCategories()
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = AssessmentCategory::where('is_active', true)
            ->get(['id', 'name', 'description']);

        return response()->json([
            'data' => $data
        ]);
    }

    public function simpanAssessment(Request $request)
    {
        $request->validate([
            'evaluatee_id' => 'required|exists:users,id',
            'period' => 'required|string',
            'scores' => 'required|array',
            'scores.*.category_id' => 'required|exists:assessment_categories,id',
            'scores.*.score' => 'required|numeric|min:1|max:5'
        ]);

        $guru = JWTAuth::parseToken()->authenticate();

        if ($guru->role !== 'guru') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();

        try {

            // cek sudah ada penilaian bulan ini belum
            $cek = Assessment::where('evaluatee_id', $request->evaluatee_id)
                ->where('evaluator_id', $guru->id)
                ->where('period', $request->period)
                ->first();

            if ($cek) {
                return response()->json([
                    'message' => 'Penilaian bulan ini sudah ada'
                ], 400);
            }

            $assessment = Assessment::create([
                'evaluator_id' => $guru->id,
                'evaluatee_id' => $request->evaluatee_id,
                'assessment_date' => now(),
                'period' => $request->period,
                'general_notes' => $request->general_notes
            ]);

            foreach ($request->scores as $score) {

                AssessmentDetail::create([
                    'assessment_id' => $assessment->id,
                    'category_id' => $score['category_id'],
                    'score' => $score['score']
                ]);

            }

            DB::commit();

            return response()->json([
                'message' => 'Penilaian berhasil disimpan'
            ]);

        }
        catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menyimpan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function nilaiMurid(Request $request, $muridId)    {
        $guru = JWTAuth::parseToken()->authenticate();

        $period = $request->query('period');

        $query = Assessment::with('details.category')
            ->where('evaluatee_id', $muridId)
            ->where('evaluator_id', $guru->id);

        if ($period) {
            $query->where('period', 'like', $period . '%');
        }

        $data = $query->orderBy('assessment_date', 'desc')->get();

        return response()->json([
            'data' => $data
        ]);    }
}
