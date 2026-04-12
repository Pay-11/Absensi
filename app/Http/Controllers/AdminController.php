<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Absensi;
use App\Models\Mapel;
use App\Models\GuruMapel;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Ambil data statistik
        $totalSiswa = User::where('role', 'siswa')->count();
        $totalGuru = User::where('role', 'guru')->count();
        $totalKelas = Kelas::count();
        $totalMapel = Mapel::count();
        $totalAbsensiHariIni = Absensi::whereDate('created_at', today())->count();

        return view('admin.dashboard', compact(
            'totalSiswa',
            'totalGuru',
            'totalKelas',
            'totalMapel',
            'totalAbsensiHariIni'
        ));
    }

    public function penilaianSikap()
    {
        $penilaians = \App\Models\PenilaianSikap::with(['siswa', 'guru'])
                        ->orderBy('tanggal', 'desc')
                        ->get();

        return view('pages.penilaian_sikap', compact('penilaians'));
    }
}