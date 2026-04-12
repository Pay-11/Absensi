<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\SesiAbsen;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GuruController extends Controller
{
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
