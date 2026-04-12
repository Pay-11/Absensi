<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class SiswaController extends Controller
{
    // ========================
    // SISWA (USER)
    // ========================
    public function dashboard()
    {
        return view('siswa.dashboard');
    }

    public function scan()
    {
        return view('siswa.scan');
    }

    public function history()
    {
        return view('siswa.history');
    }

    public function subjects()
    {
        return view('siswa.subjects');
    }

    public function schedule()
    {
        return view('siswa.schedule');
    }

    // ========================
    // ADMIN CRUD SISWA
    // ========================

    public function index()
    {
        $siswa = User::where('role', 'murid')
            ->with('anggotaKelas.kelas')
            ->get();

        return view('pages.siswa.index', compact('siswa'));
    }

    public function edit($id)
    {
        $siswa = User::findOrFail($id);
        return view('pages.siswa.edit', compact('siswa'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'nisn' => 'required'
        ]);

        $siswa = User::findOrFail($id);

        $siswa->update([
            'name' => $request->name,
            'nisn' => $request->nisn,
        ]);

        return redirect()->route('siswa.index')
            ->with('success', 'Berhasil update');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return redirect()->route('siswa.index')
            ->with('success', 'Berhasil hapus');
    }
}