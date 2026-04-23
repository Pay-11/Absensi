<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\TahunAjar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class KelasController extends Controller
{
    private function authorizeAdmin()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return in_array($user->role, ['superadmin', 'admin']) ? $user : null;
    }

    // GET /api/kelas
    public function index(Request $request)
    {
        if (!$this->authorizeAdmin()) return response()->json(['message' => 'Unauthorized'], 403);

        $query = Kelas::with(['tahunAjar', 'waliGuru']);

        if ($request->filled('tahun_ajar_id')) {
            $query->where('tahun_ajar_id', $request->tahun_ajar_id);
        }

        return response()->json([
            'message' => 'Data kelas berhasil diambil',
            'data'    => $query->orderBy('nama_kelas')->get()->map(fn($k) => $this->format($k)),
        ]);
    }

    // GET /api/kelas/{id}
    public function show($id)
    {
        if (!$this->authorizeAdmin()) return response()->json(['message' => 'Unauthorized'], 403);

        $kelas = Kelas::with(['tahunAjar', 'waliGuru'])->findOrFail($id);

        return response()->json(['message' => 'Data kelas ditemukan', 'data' => $this->format($kelas)]);
    }

    // POST /api/kelas
    public function store(Request $request)
    {
        if (!$this->authorizeAdmin()) return response()->json(['message' => 'Unauthorized'], 403);

        $request->validate([
            'nama_kelas'    => 'required|string|max:100',
            'tahun_ajar_id' => 'required|exists:tahun_ajar,id',
            'wali_guru_id'  => 'nullable|exists:users,id',
        ]);

        $kelas = Kelas::create($request->only(['nama_kelas', 'tahun_ajar_id', 'wali_guru_id']));

        return response()->json([
            'message' => 'Kelas berhasil ditambahkan',
            'data'    => $this->format($kelas->load(['tahunAjar', 'waliGuru'])),
        ], 201);
    }

    // PUT /api/kelas/{id}
    public function update(Request $request, $id)
    {
        if (!$this->authorizeAdmin()) return response()->json(['message' => 'Unauthorized'], 403);

        $kelas = Kelas::findOrFail($id);

        $request->validate([
            'nama_kelas'    => 'sometimes|required|string|max:100',
            'tahun_ajar_id' => 'sometimes|required|exists:tahun_ajar,id',
            'wali_guru_id'  => 'nullable|exists:users,id',
        ]);

        $kelas->update($request->only(['nama_kelas', 'tahun_ajar_id', 'wali_guru_id']));

        return response()->json([
            'message' => 'Kelas berhasil diperbarui',
            'data'    => $this->format($kelas->fresh()->load(['tahunAjar', 'waliGuru'])),
        ]);
    }

    // DELETE /api/kelas/{id}
    public function destroy($id)
    {
        if (!$this->authorizeAdmin()) return response()->json(['message' => 'Unauthorized'], 403);

        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return response()->json(['message' => 'Kelas berhasil dihapus']);
    }

    private function format(Kelas $k): array
    {
        return [
            'id'            => $k->id,
            'nama_kelas'    => $k->nama_kelas,
            'tahun_ajar'    => $k->tahunAjar?->nama,
            'tahun_ajar_id' => $k->tahun_ajar_id,
            'wali_guru'     => $k->waliGuru?->name,
            'wali_guru_id'  => $k->wali_guru_id,
        ];
    }
}
