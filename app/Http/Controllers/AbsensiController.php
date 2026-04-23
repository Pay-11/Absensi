<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\SesiAbsen;
use App\Models\User;
use App\Http\Controllers\Concerns\ExportsCsv;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    use ExportsCsv;
    /**
     * Rekap absensi per kelas & rentang tanggal
     */
    public function rekap(Request $request)
    {
        $kelas    = Kelas::with('tahunAjar')->orderBy('nama_kelas')->get();
        $mapel    = Mapel::orderBy('nama_mapel')->get();

        // Filter default: kelas pertama & bulan ini
        $kelasId    = $request->kelas_id;
        $mapelId    = $request->mapel_id;
        $tglMulai   = $request->tgl_mulai  ?? now()->startOfMonth()->toDateString();
        $tglSelesai = $request->tgl_selesai ?? now()->toDateString();

        $rekap   = collect();
        $siswaList = collect();
        $sesiList  = collect();

        if ($kelasId) {
            // Ambil semua siswa di kelas ini
            $siswaList = User::where('role', 'murid')
                ->whereHas('kelas', fn($q) => $q->where('kelas.id', $kelasId))
                ->orderBy('name')
                ->get();

            // Ambil sesi absen di kelas & rentang tanggal
            $sesiQuery = SesiAbsen::with(['jadwal.mapel', 'jadwal.kelas'])
                ->whereHas('jadwal', fn($q) => $q->where('kelas_id', $kelasId))
                ->whereBetween('tanggal', [$tglMulai, $tglSelesai]);

            if ($mapelId) {
                $sesiQuery->whereHas('jadwal', fn($q) => $q->where('mapel_id', $mapelId));
            }

            $sesiList = $sesiQuery->orderBy('tanggal')->get();

            // Buat rekap: [murid_id][sesi_id] = status
            $absensiData = Absensi::whereIn('sesi_absen_id', $sesiList->pluck('id'))
                ->get()
                ->groupBy('murid_id');

            foreach ($siswaList as $siswa) {
                $row = [
                    'siswa'   => $siswa,
                    'detail'  => [],
                    'hadir'   => 0,
                    'izin'    => 0,
                    'alpha'   => 0,
                ];

                foreach ($sesiList as $sesi) {
                    $absensi = $absensiData->get($siswa->id)?->firstWhere('sesi_absen_id', $sesi->id);
                    $status  = $absensi?->status ?? 'alpha';
                    $row['detail'][$sesi->id] = $status;

                    if ($status === 'hadir') $row['hadir']++;
                    elseif ($status === 'izin') $row['izin']++;
                    else $row['alpha']++;
                }

                $rekap->push($row);
            }
        }

        return view('pages.absensi.rekap', compact(
            'kelas', 'mapel', 'rekap', 'sesiList', 'siswaList',
            'kelasId', 'mapelId', 'tglMulai', 'tglSelesai'
        ));
    }

    /**
     * Detail satu sesi absen (per sesi_id)
     */
    public function detail($sesiId)
    {
        $sesi     = SesiAbsen::with(['jadwal.kelas','jadwal.mapel','jadwal.guru','dibukaOleh'])->findOrFail($sesiId);
        $absensi  = Absensi::with('murid')
            ->where('sesi_absen_id', $sesiId)
            ->orderBy('waktu_scan')
            ->get();

        return view('pages.absensi.detail', compact('sesi', 'absensi'));
    }

    /**
     * Export rekap absensi ke Excel
     */
    public function exportRekap(Request $request)
    {
        $kelasId    = $request->kelas_id;
        $mapelId    = $request->mapel_id;
        $tglMulai   = $request->tgl_mulai   ?? now()->startOfMonth()->toDateString();
        $tglSelesai = $request->tgl_selesai ?? now()->toDateString();

        if (!$kelasId) {
            return back()->with('error', 'Pilih kelas terlebih dahulu');
        }

        $siswaList = User::where('role', 'murid')
            ->whereHas('kelas', fn($q) => $q->where('kelas.id', $kelasId))
            ->orderBy('name')->get();

        $sesiQuery = SesiAbsen::with(['jadwal.mapel'])
            ->whereHas('jadwal', fn($q) => $q->where('kelas_id', $kelasId))
            ->whereBetween('tanggal', [$tglMulai, $tglSelesai]);

        if ($mapelId) {
            $sesiQuery->whereHas('jadwal', fn($q) => $q->where('mapel_id', $mapelId));
        }

        $sesiList    = $sesiQuery->orderBy('tanggal')->get();
        $absensiData = Absensi::whereIn('sesi_absen_id', $sesiList->pluck('id'))
            ->get()->groupBy('murid_id');

        // Buat heading: Nama, NISN, [tgl sesi...], H, I, A, Sakit, Terlambat, %
        $headings = ['Nama Siswa', 'NISN'];
        foreach ($sesiList as $sesi) {
            $headings[] = Carbon::parse($sesi->tanggal)->format('d/m') .
                          '\n' . ($sesi->jadwal?->mapel?->kode_mapel ?? '-');
        }
        $headings = array_merge($headings, ['Hadir', 'Izin', 'Sakit', 'Alpha', 'Terlambat', '%']);

        // Buat baris data
        $rows = [];
        foreach ($siswaList as $siswa) {
            $row = [$siswa->name, $siswa->nisn ?? '-'];
            $h = $i = $s = $a = $t = 0;

            foreach ($sesiList as $sesi) {
                $absensi = $absensiData->get($siswa->id)?->firstWhere('sesi_absen_id', $sesi->id);
                $status  = $absensi?->status ?? 'alpha';
                $row[]   = strtoupper(substr($status, 0, 1)); // H/I/A/S/T

                match ($status) {
                    'hadir'     => $h++,
                    'izin'      => $i++,
                    'sakit'     => $s++,
                    'alpha'     => $a++,
                    'terlambat' => $t++,
                    default     => null,
                };
            }

            $total = $h + $i + $s + $a + $t;
            $pct   = $total > 0 ? round($h / $total * 100) . '%' : '0%';
            array_push($row, $h, $i, $s, $a, $t, $pct);
            $rows[] = $row;
        }

        $kelas    = Kelas::find($kelasId);
        $filename = 'rekap-absensi-' . ($kelas?->nama_kelas ?? 'kelas') . '-' . now()->format('Ymd');

        return $this->csvResponse($headings, $rows, $filename, '155e75', 'ecfeff');
    }
}
