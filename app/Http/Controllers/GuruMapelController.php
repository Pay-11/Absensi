<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Mapel;
use App\Http\Controllers\Concerns\ExportsCsv;
use Illuminate\Support\Facades\DB;

class GuruMapelController extends Controller
{
    use ExportsCsv;
    /**
     * Tampilkan semua guru beserta mapel-nya.
     * Bisa filter by guru_id via ?guru_id=
     */
    public function index(Request $request)
    {
        $guruList = User::where('role', 'guru')->orderBy('name')->get();
        $mapelList = Mapel::orderBy('nama_mapel')->get();

        $selectedGuru  = null;
        $mapelDiajar   = collect();
        $mapelOptions  = collect();

        if ($request->filled('guru_id')) {
            $selectedGuru = User::where('role', 'guru')->findOrFail($request->guru_id);

            // Mapel yang sudah diajarkan guru ini
            $mapelDiajar = $selectedGuru->mapel()->orderBy('nama_mapel')->get();

            // Mapel yang belum ditugaskan ke guru ini
            $sudahDitugaskan = $mapelDiajar->pluck('id');
            $mapelOptions = Mapel::whereNotIn('id', $sudahDitugaskan)
                ->orderBy('nama_mapel')
                ->get();
        }

        return view('pages.guru-mapel.index', compact(
            'guruList', 'mapelList', 'selectedGuru', 'mapelDiajar', 'mapelOptions'
        ));
    }

    /**
     * Tugaskan satu atau banyak mapel ke guru
     */
    public function store(Request $request)
    {
        $request->validate([
            'guru_id'    => 'required|exists:users,id',
            'mapel_ids'  => 'required|array|min:1',
            'mapel_ids.*'=> 'exists:mapel,id',
        ]);

        $guru   = User::where('role', 'guru')->findOrFail($request->guru_id);
        $added  = 0;
        $skipped = 0;

        foreach ($request->mapel_ids as $mapelId) {
            $exists = DB::table('guru_mapel')
                ->where('guru_id', $guru->id)
                ->where('mapel_id', $mapelId)
                ->exists();

            if (!$exists) {
                DB::table('guru_mapel')->insert([
                    'guru_id'    => $guru->id,
                    'mapel_id'   => $mapelId,
                    'created_at' => now(),
                ]);
                $added++;
            } else {
                $skipped++;
            }
        }

        if ($added > 0 && $skipped === 0) {
            $msg = "$added mata pelajaran berhasil ditugaskan ke {$guru->name}";
        } elseif ($added > 0) {
            $msg = "$added mapel ditambahkan, $skipped sudah ada sebelumnya";
        } else {
            $msg = 'Semua mapel yang dipilih sudah ditugaskan ke guru ini';
        }

        $type = $added > 0 ? 'success' : 'error';

        return redirect()->route('guru-mapel.index', ['guru_id' => $guru->id])
            ->with($type, $msg);
    }

    /**
     * Hapus penugasan mapel dari guru
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'guru_id'  => 'required|exists:users,id',
            'mapel_id' => 'required|exists:mapel,id',
        ]);

        $deleted = DB::table('guru_mapel')
            ->where('guru_id', $request->guru_id)
            ->where('mapel_id', $request->mapel_id)
            ->delete();

        if ($deleted) {
            $mapel = Mapel::find($request->mapel_id);
            $msg = "Mapel {$mapel->nama_mapel} berhasil dihapus dari penugasan";
        } else {
            $msg = 'Data tidak ditemukan';
        }

        return redirect()->route('guru-mapel.index', ['guru_id' => $request->guru_id])
            ->with($deleted ? 'success' : 'error', $msg);
    }

    public function export(Request $request)
    {
        $guruId = $request->guru_id ?? null;
        $query  = DB::table('guru_mapel')
            ->join('users', 'users.id', '=', 'guru_mapel.guru_id')
            ->join('mapel', 'mapel.id', '=', 'guru_mapel.mapel_id')
            ->select('users.name as guru_name', 'users.nip', 'mapel.nama_mapel', 'mapel.kode_mapel', 'guru_mapel.created_at');

        if ($guruId) $query->where('guru_mapel.guru_id', $guruId);
        $data = $query->orderBy('users.name')->orderBy('mapel.nama_mapel')->get();

        $headers = ['#', 'Nama Guru', 'NIP', 'Mata Pelajaran', 'Kode Mapel', 'Ditugaskan'];
        $rows = [];
        foreach ($data as $i => $row) {
            $rows[] = [$i+1, $row->guru_name, $row->nip ?? '-', $row->nama_mapel, $row->kode_mapel ?? '-', $row->created_at];
        }

        $filename = $guruId ? 'guru-mapel-' . $guruId : 'guru-mapel-semua';
        return $this->csvResponse($headers, $rows, $filename . '-' . now()->format('Ymd'), '3a5a1a', 'f2f7ee');
    }
}
