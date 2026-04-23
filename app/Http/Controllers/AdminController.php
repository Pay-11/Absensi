<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Absensi;
use App\Models\Mapel;
use App\Models\GuruMapel;

class AdminController extends Controller
{
    // ============================
    // CRUD AKUN ADMIN (superadmin only)
    // ============================

    public function adminIndex()
    {
        $admins = User::where('role', 'admin')
            ->orderBy('name')
            ->get();

        return view('pages.admin.index', compact('admins'));
    }

    public function adminStore(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'admin',
        ]);

        return redirect()->route('admin.accounts.index')
            ->with('success', 'Admin berhasil ditambahkan');
    }

    public function adminEdit($id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        return view('pages.admin.edit', compact('admin'));
    }

    public function adminUpdate(Request $request, $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($admin->id)],
        ]);

        $data = $request->only(['name', 'email']);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6']);
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->route('admin.accounts.index')
            ->with('success', 'Data admin berhasil diperbarui');
    }

    public function adminDestroy($id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        $admin->delete();

        return redirect()->route('admin.accounts.index')
            ->with('success', 'Admin berhasil dihapus');
    }

    // ============================
    // DASHBOARD
    // ============================
    public function dashboard()
    {
        // Ambil data statistik
        $totalSiswa          = User::where('role', 'murid')->count();
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