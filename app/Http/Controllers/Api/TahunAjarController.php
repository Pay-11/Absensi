<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TahunAjar;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class TahunAjarController extends Controller
{
    private function authorizeAdmin()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return in_array($user->role, ['superadmin', 'admin']) ? $user : null;
    }

    // GET /api/tahun-ajar
    public function index()
    {
        $auth = $this->authorizeAdmin();
        if (!$auth) return response()->json(['message' => 'Unauthorized'], 403);

        $data = TahunAjar::orderByDesc('aktif')->orderByDesc('id')->get();

        return response()->json([
            'message' => 'Data tahun ajar berhasil diambil',
            'data'    => $data,
        ]);
    }

    // GET /api/tahun-ajar/{id}
    public function show($id)
    {
        $auth = $this->authorizeAdmin();
        if (!$auth) return response()->json(['message' => 'Unauthorized'], 403);

        return response()->json([
            'message' => 'Data tahun ajar ditemukan',
            'data'    => TahunAjar::findOrFail($id),
        ]);
    }

    // POST /api/tahun-ajar
    public function store(Request $request)
    {
        $auth = $this->authorizeAdmin();
        if (!$auth) return response()->json(['message' => 'Unauthorized'], 403);

        $request->validate([
            'nama'  => 'required|string|max:20|unique:tahun_ajar,nama',
            'aktif' => 'sometimes|boolean',
        ]);

        // Kalau set aktif = true, nonaktifkan yang lain dulu
        if ($request->boolean('aktif')) {
            TahunAjar::query()->update(['aktif' => false]);
        }

        $tahunAjar = TahunAjar::create([
            'nama'  => $request->nama,
            'aktif' => $request->boolean('aktif', false),
        ]);

        return response()->json([
            'message' => 'Tahun ajar berhasil ditambahkan',
            'data'    => $tahunAjar,
        ], 201);
    }

    // PUT /api/tahun-ajar/{id}
    public function update(Request $request, $id)
    {
        $auth = $this->authorizeAdmin();
        if (!$auth) return response()->json(['message' => 'Unauthorized'], 403);

        $tahunAjar = TahunAjar::findOrFail($id);

        $request->validate([
            'nama'  => 'sometimes|required|string|max:20|unique:tahun_ajar,nama,' . $id,
            'aktif' => 'sometimes|boolean',
        ]);

        if ($request->boolean('aktif')) {
            TahunAjar::where('id', '!=', $id)->update(['aktif' => false]);
        }

        $tahunAjar->update([
            'nama'  => $request->get('nama', $tahunAjar->nama),
            'aktif' => $request->has('aktif') ? $request->boolean('aktif') : $tahunAjar->aktif,
        ]);

        return response()->json([
            'message' => 'Tahun ajar berhasil diperbarui',
            'data'    => $tahunAjar->fresh(),
        ]);
    }

    // DELETE /api/tahun-ajar/{id}
    public function destroy($id)
    {
        $auth = $this->authorizeAdmin();
        if (!$auth) return response()->json(['message' => 'Unauthorized'], 403);

        $tahunAjar = TahunAjar::findOrFail($id);

        if ($tahunAjar->aktif) {
            return response()->json([
                'message' => 'Tidak bisa menghapus tahun ajar yang sedang aktif.'
            ], 422);
        }

        $tahunAjar->delete();

        return response()->json(['message' => 'Tahun ajar berhasil dihapus']);
    }

    // POST /api/tahun-ajar/{id}/aktifkan
    public function aktifkan($id)
    {
        $auth = $this->authorizeAdmin();
        if (!$auth) return response()->json(['message' => 'Unauthorized'], 403);

        TahunAjar::query()->update(['aktif' => false]);
        $tahunAjar = TahunAjar::findOrFail($id);
        $tahunAjar->update(['aktif' => true]);

        return response()->json([
            'message' => "Tahun ajar {$tahunAjar->nama} berhasil diaktifkan",
            'data'    => $tahunAjar,
        ]);
    }
}
