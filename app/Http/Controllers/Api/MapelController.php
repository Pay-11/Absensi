<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class MapelController extends Controller
{
    private function authorizeAdmin()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return in_array($user->role, ['superadmin', 'admin']) ? $user : null;
    }

    public function index()
    {
        if (!$this->authorizeAdmin()) return response()->json(['message' => 'Unauthorized'], 403);

        return response()->json([
            'message' => 'Data mapel berhasil diambil',
            'data'    => Mapel::orderBy('nama_mapel')->get(),
        ]);
    }

    public function show($id)
    {
        if (!$this->authorizeAdmin()) return response()->json(['message' => 'Unauthorized'], 403);

        return response()->json([
            'message' => 'Data mapel ditemukan',
            'data'    => Mapel::findOrFail($id),
        ]);
    }

    public function store(Request $request)
    {
        if (!$this->authorizeAdmin()) return response()->json(['message' => 'Unauthorized'], 403);

        $request->validate([
            'nama_mapel' => 'required|string|max:100',
            'kode_mapel' => 'required|string|max:20|unique:mapel,kode_mapel',
        ]);

        $mapel = Mapel::create($request->only(['nama_mapel', 'kode_mapel']));

        return response()->json(['message' => 'Mapel berhasil ditambahkan', 'data' => $mapel], 201);
    }

    public function update(Request $request, $id)
    {
        if (!$this->authorizeAdmin()) return response()->json(['message' => 'Unauthorized'], 403);

        $mapel = Mapel::findOrFail($id);

        $request->validate([
            'nama_mapel' => 'sometimes|required|string|max:100',
            'kode_mapel' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('mapel', 'kode_mapel')->ignore($id)],
        ]);

        $mapel->update($request->only(['nama_mapel', 'kode_mapel']));

        return response()->json(['message' => 'Mapel berhasil diperbarui', 'data' => $mapel->fresh()]);
    }

    public function destroy($id)
    {
        if (!$this->authorizeAdmin()) return response()->json(['message' => 'Unauthorized'], 403);

        Mapel::findOrFail($id)->delete();

        return response()->json(['message' => 'Mapel berhasil dihapus']);
    }
}
