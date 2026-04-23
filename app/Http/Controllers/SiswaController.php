<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Concerns\ExportsCsv;

class SiswaController extends Controller
{
    use ExportsCsv;
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
        // Relationship di User model bernama 'kelas' (belongsToMany via anggota_kelas)
        $siswa = User::where('role', 'murid')
            ->with('kelas')
            ->orderBy('name')
            ->get();

        return view('pages.siswa.index', compact('siswa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'nisn'     => 'required|string|max:20|unique:users,nisn',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'nisn'     => $request->nisn,
            'password' => bcrypt($request->password),
            'role'     => 'murid',
        ]);

        return redirect()->route('siswa.index')
            ->with('success', 'Siswa berhasil ditambahkan');
    }

    public function edit($id)
    {
        $siswa = User::where('role', 'murid')->findOrFail($id);
        return view('pages.siswa.edit', compact('siswa'));
    }

    public function update(Request $request, $id)
    {
        $siswa = User::where('role', 'murid')->findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $siswa->id,
            'nisn'  => 'required|string|max:20|unique:users,nisn,' . $siswa->id,
        ]);

        $data = $request->only(['name', 'email', 'nisn']);

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $siswa->update($data);

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui');
    }

    public function destroy($id)
    {
        $siswa = User::where('role', 'murid')->findOrFail($id);
        $siswa->delete();

        return redirect()->route('siswa.index')
            ->with('success', 'Siswa berhasil dihapus');
    }

    public function export()
    {
        $siswa = User::where('role', 'murid')->with('kelas')->orderBy('name')->get();

        $headers = ['#', 'Nama Lengkap', 'Email', 'NISN', 'Kelas', 'Terdaftar Sejak'];
        $rows = [];
        foreach ($siswa as $i => $s) {
            $rows[] = [
                $i + 1,
                $s->name,
                $s->email,
                $s->nisn ?? '-',
                $s->kelas->pluck('nama_kelas')->join(', ') ?: 'Belum ada kelas',
                $s->created_at->format('d/m/Y'),
            ];
        }

        return $this->csvResponse($headers, $rows, 'data-siswa-' . now()->format('Ymd'), '1a7431', 'e6f4ea');
    }
}