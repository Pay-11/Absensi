<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKelas;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required'
        ]);

        $login = $request->login;
        $password = $request->password;

        $user = User::where('email', $login)
            ->orWhere('nip', $login)
            ->orWhere('nisn', $login)
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 401);
        }

        if (
            !$token = JWTAuth::attempt([
                'email' => $user->email,
                'password' => $password
            ])
        ) {
            return response()->json([
                'message' => 'Password salah'
            ], 401);
        }

        $kelasData = null;

        if ($user->role === 'murid') {

            $anggota = AnggotaKelas::with([
                'kelas.tahunAjar',
                'kelas.waliGuru'
            ])
                ->where('murid_id', $user->id)
                ->first();

            if ($anggota) {
                $kelasData = $anggota->kelas;
            }
        }

        return response()->json([
            'token' => $token,
            'user' => $user,
            'kelas' => $kelasData
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = auth()->user();

        // cek password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Kata sandi saat ini salah'
            ], 400);
        }

        // update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Kata sandi berhasil diubah'
        ]);
    }



    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'message' => 'Logout sukses'
        ]);
    }
}