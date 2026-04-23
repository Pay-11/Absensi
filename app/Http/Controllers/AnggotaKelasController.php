<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnggotaKelas;
use App\Models\Kelas;
use App\Models\User;
use App\Http\Controllers\Concerns\ExportsCsv;

class AnggotaKelasController extends Controller
{
    use ExportsCsv;
    /**
     * Tampilkan daftar anggota kelas (pilih kelas dulu via query param ?kelas_id=)
     */
    public function index(Request $request)
    {
        $kelasList = Kelas::with('tahunAjar')->orderBy('nama_kelas')->get();

        $selectedKelas = null;
        $anggota       = collect();
        $siswaOptions  = collect(); // siswa yang belum masuk kelas ini

        if ($request->filled('kelas_id')) {
            $selectedKelas = Kelas::with(['tahunAjar', 'waliGuru'])->findOrFail($request->kelas_id);

            $anggota = AnggotaKelas::with('murid')
                ->where('kelas_id', $selectedKelas->id)
                ->get();

            // Ambil semua murid_id yang SUDAH terdaftar di kelas manapun
            $sudahPunyaKelas = AnggotaKelas::pluck('murid_id');

            $siswaOptions = User::where('role', 'murid')
                ->whereNotIn('id', $sudahPunyaKelas)
                ->orderBy('name')
                ->get();
        }

        return view('pages.anggota-kelas.index', compact(
            'kelasList', 'selectedKelas', 'anggota', 'siswaOptions'
        ));
    }

    /**
     * Tambah satu atau banyak siswa ke kelas
     */
    public function store(Request $request)
    {
        $request->validate([
            'kelas_id'   => 'required|exists:kelas,id',
            'murid_ids'  => 'required|array|min:1',
            'murid_ids.*'=> 'exists:users,id',
        ]);

        $kelasId  = $request->kelas_id;
        $added    = 0;
        $skipped  = [];

        foreach ($request->murid_ids as $muridId) {
            // Cek apakah siswa sudah terdaftar di KELAS MANAPUN
            $sudahDiKelas = AnggotaKelas::where('murid_id', $muridId)->first();

            if ($sudahDiKelas) {
                $nama = \App\Models\User::find($muridId)?->name ?? 'Siswa';
                $skipped[] = $nama;
                continue;
            }

            AnggotaKelas::create([
                'kelas_id' => $kelasId,
                'murid_id' => $muridId,
            ]);
            $added++;
        }

        if ($added > 0 && count($skipped) === 0) {
            $msg = "$added siswa berhasil ditambahkan ke kelas";
        } elseif ($added > 0 && count($skipped) > 0) {
            $msg = "$added siswa ditambahkan. Dilewati karena sudah punya kelas: " . implode(', ', $skipped);
        } else {
            $msg = 'Semua siswa yang dipilih sudah terdaftar di kelas lain: ' . implode(', ', $skipped);
        }

        $type = $added > 0 ? 'success' : 'error';

        return redirect()->route('anggota-kelas.index', ['kelas_id' => $kelasId])
            ->with($type, $msg);
    }

    /**
     * Hapus satu siswa dari kelas
     */
    public function destroy($id)
    {
        $anggota = AnggotaKelas::findOrFail($id);
        $kelasId = $anggota->kelas_id;
        $anggota->delete();

        return redirect()->route('anggota-kelas.index', ['kelas_id' => $kelasId])
            ->with('success', 'Siswa berhasil dikeluarkan dari kelas');
    }

    /**
     * Pindahkan siswa ke kelas lain
     */
    public function move(Request $request, $id)
    {
        $request->validate([
            'kelas_tujuan_id' => 'required|exists:kelas,id|different:kelas_id',
        ]);

        $anggota = AnggotaKelas::findOrFail($id);
        $kelasAsal = $anggota->kelas_id;

        // cek sudah ada di kelas tujuan
        $exists = AnggotaKelas::where('kelas_id', $request->kelas_tujuan_id)
            ->where('murid_id', $anggota->murid_id)
            ->exists();

        if ($exists) {
            return redirect()->route('anggota-kelas.index', ['kelas_id' => $kelasAsal])
                ->with('error', 'Siswa sudah terdaftar di kelas tujuan');
        }

        $anggota->update(['kelas_id' => $request->kelas_tujuan_id]);

        return redirect()->route('anggota-kelas.index', ['kelas_id' => $request->kelas_tujuan_id])
            ->with('success', 'Siswa berhasil dipindahkan ke kelas tujuan');
    }

    public function export(Request $request)
    {
        $kelasId = $request->kelas_id ?? null;
        $query   = AnggotaKelas::with(['murid', 'kelas.tahunAjar']);
        if ($kelasId) $query->where('kelas_id', $kelasId);
        $data = $query->get();

        $headers = ['#', 'Nama Siswa', 'Email', 'NISN', 'Kelas', 'Tahun Ajaran'];
        $rows = [];
        foreach ($data as $i => $row) {
            $rows[] = [
                $i + 1,
                $row->murid->name ?? '-',
                $row->murid->email ?? '-',
                $row->murid->nisn ?? '-',
                $row->kelas->nama_kelas ?? '-',
                $row->kelas->tahunAjar->nama ?? '-',
            ];
        }

        $filename = $kelasId ? 'anggota-kelas-' . $kelasId : 'anggota-kelas-semua';
        return $this->csvResponse($headers, $rows, $filename . '-' . now()->format('Ymd'), '6a1a7a', 'f7f0fa');
    }
}
