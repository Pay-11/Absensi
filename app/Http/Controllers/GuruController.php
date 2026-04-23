<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\SesiAbsen;
use App\Models\User;
use App\Http\Controllers\Concerns\ExportsCsv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class GuruController extends Controller
{
    use ExportsCsv;
    // ============================
    // ADMIN CRUD GURU
    // ============================

    public function adminIndex()
    {
        $guru = User::where('role', 'guru')
            ->with('mapel')
            ->orderBy('name')
            ->get();

        return view('pages.guru.index', compact('guru'));
    }

    public function adminStore(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'nip'      => 'nullable|string|max:20|unique:users,nip',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'guru',
            'nip'      => $request->nip ?: null,
        ]);

        return redirect()->route('guru.admin.index')
            ->with('success', 'Guru berhasil ditambahkan');
    }

    public function adminEdit($id)
    {
        $guru = User::where('role', 'guru')->findOrFail($id);
        return view('pages.guru.edit', compact('guru'));
    }

    public function adminUpdate(Request $request, $id)
    {
        $guru = User::where('role', 'guru')->findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($guru->id)],
            'nip'   => ['nullable', 'string', 'max:20', Rule::unique('users', 'nip')->ignore($guru->id)],
        ]);

        $data = $request->only(['name', 'email', 'nip']);
        $data['nip'] = $request->nip ?: null;

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6']);
            $data['password'] = Hash::make($request->password);
        }

        $guru->update($data);

        return redirect()->route('guru.admin.index')
            ->with('success', 'Data guru berhasil diperbarui');
    }

    public function adminDestroy($id)
    {
        $guru = User::where('role', 'guru')->findOrFail($id);
        $guru->delete();

        return redirect()->route('guru.admin.index')
            ->with('success', 'Guru berhasil dihapus');
    }

    public function export()
    {
        $guru = User::where('role', 'guru')->with('mapel')->orderBy('name')->get();

        $headers = ['#', 'Nama Lengkap', 'Email', 'NIP', 'Mata Pelajaran', 'Terdaftar Sejak'];
        $rows = [];
        foreach ($guru as $i => $g) {
            $rows[] = [
                $i + 1,
                $g->name,
                $g->email,
                $g->nip ?? '-',
                $g->mapel->pluck('nama_mapel')->join('; ') ?: 'Belum ditugaskan',
                $g->created_at->format('d/m/Y'),
            ];
        }

        return $this->csvResponse($headers, $rows, 'data-guru-' . now()->format('Ymd'), '1a3a7a', 'eef2fa');
    }


    /**
     * Show the Guru Dashboard.
     */
    public function dashboard(Request $request)
    {
        // For now, returning the UI view
        return view('guru.dashboard');
    }

    /**
     * Show the Guru Profile.
     */
    public function profile()
    {
        return view('guru.profile');
    }

    /**
     * Initialize or fetch the active QR session and show the View.
     */
    public function qr(Request $request)
    {
        $jadwal_id = $request->query('jadwal_id', 1); // fallback to 1 for testing
        
        // Find existing active session for today
        $sesi = SesiAbsen::where('jadwal_id', $jadwal_id)
            ->whereDate('tanggal', Carbon::today())
            ->where('tipe', 'jam_masuk')
            ->first();

        // Need authentication to get user (dibuka_oleh). Mocking for now:
        $guru_id = 1;

        if (!$sesi) {
            $sesi = SesiAbsen::create([
                'jadwal_id' => $jadwal_id,
                'tipe' => 'jam_masuk',
                'tanggal' => Carbon::today(),
                'token_qr' => $this->generateSecureToken(),
                'expired_at' => Carbon::now()->addSeconds(15),
                'dibuka_oleh' => $guru_id,
                'dibuka_pada' => Carbon::now(),
            ]);
        } else {
            // Update token if resuming session
            $sesi->update([
                'token_qr' => $this->generateSecureToken(),
                'expired_at' => Carbon::now()->addSeconds(15)
            ]);
        }

        return view('guru.qr', compact('sesi'));
    }

    /**
     * API Endpoint to Refresh QR Token.
     */
    public function refreshQr(Request $request)
    {
        $request->validate([
            'sesi_id' => 'required|exists:sesi_absen,id'
        ]);

        $sesi = SesiAbsen::findOrFail($request->sesi_id);
        
        $newToken = $this->generateSecureToken();
        $sesi->update([
            'token_qr' => $newToken,
            'expired_at' => Carbon::now()->addSeconds(15)
        ]);

        return response()->json([
            'success' => true,
            'token' => $newToken,
            'expires_at' => $sesi->expired_at
        ]);
    }
    
    /**
     * Helper to close an active session.
     */
    public function closeSesi(Request $request)
    {
        $request->validate([
            'sesi_id' => 'required|exists:sesi_absen,id'
        ]);

        $sesi = SesiAbsen::findOrFail($request->sesi_id);
        // Expire the token to close session
        $sesi->update([
            'expired_at' => Carbon::now()->subMinutes(1)
        ]);

        return redirect('/guru/dashboard')->with('success', 'Sesi absensi berhasil ditutup.');
    }

    /**
     * Helper to generate random secure token.
     */
    private function generateSecureToken()
    {
        return 'sehadir_' . Str::random(12) . '_' . Carbon::now()->timestamp;
    }
}
