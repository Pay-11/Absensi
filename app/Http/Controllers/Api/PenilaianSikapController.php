<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PenilaianSikapController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:users,id',
            'sikap' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'tanggal' => 'required|date'
        ]);

        // Simulasikan bahwa pembuat data adalah user yg sedang login, 
        // namun untuk saat ini kita bisa izinkan post guru_id dari frontend atau statis.
        $guru_id = auth()->guard('api')->id() ?? $request->guru_id ?? 1;

        $penilaian = \App\Models\PenilaianSikap::create([
            'siswa_id' => $request->siswa_id,
            'guru_id' => $guru_id,
            'sikap' => $request->sikap,
            'keterangan' => $request->keterangan,
            'tanggal' => $request->tanggal,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data penilaian sikap berhasil disimpan',
            'data' => $penilaian
        ], 201);
    }
}
