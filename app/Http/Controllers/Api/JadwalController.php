<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class JadwalController extends Controller
{
    private function authorizeAdmin()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return in_array($user->role, ['superadmin', 'admin']) ? $user : null;
    }

    // GET /api/jadwal
    public function index(Request $request)
    {
        if (!$this->authorizeAdmin()) return response()->json(['message' => 'Unauthorized'], 403);

        $query = Jadwal::with(['kelas', 'mapel', 'guru']);

        if ($request->filled('kelas_id')) $query->where('kelas_id', $request->kelas_id);
        if ($request->filled('hari'))     $query->where('hari', $request->hari);
        if ($request->filled('guru_id'))  $query->where('guru_id', $request->guru_id);

        $jadwal = $query->orderByRaw("FIELD(hari,'senin','selasa','rabu','kamis','jumat')")
                        ->orderBy('jam_mulai')
                        ->get();

        return response()->json([
            'message' => 'Data jadwal berhasil diambil',
            'data'    => $jadwal->map(fn($j) => $this->format($j)),
        ]);
    }

    // GET /api/jadwal/{id}
    public function show($id)
    {
        if (!$this->authorizeAdmin()) return response()->json(['message' => 'Unauthorized'], 403);

        $jadwal = Jadwal::with(['kelas', 'mapel', 'guru'])->findOrFail($id);

        return response()->json([
            'message' => 'Data jadwal ditemukan',
            'data'    => $this->format($jadwal),
        ]);
    }

    // POST /api/jadwal
    public function store(Request $request)
    {
        if (!$this->authorizeAdmin()) return response()->json(['message' => 'Unauthorized'], 403);

        $request->validate([
            'kelas_id'   => 'required|exists:kelas,id',
            'mapel_id'   => 'required|exists:mapel,id',
            'guru_id'    => 'required|exists:users,id',
            'hari'       => ['required', Rule::in(['senin','selasa','rabu','kamis','jumat'])],
            'jam_mulai'  => 'required|date_format:H:i',
            'jam_selesai'=> 'required|date_format:H:i|after:jam_mulai',
        ]);

        $jadwal = Jadwal::create($request->only([
            'kelas_id','mapel_id','guru_id','hari','jam_mulai','jam_selesai'
        ]));

        return response()->json([
            'message' => 'Jadwal berhasil ditambahkan',
            'data'    => $this->format($jadwal->load(['kelas','mapel','guru'])),
        ], 201);
    }

    // PUT /api/jadwal/{id}
    public function update(Request $request, $id)
    {
        if (!$this->authorizeAdmin()) return response()->json(['message' => 'Unauthorized'], 403);

        $jadwal = Jadwal::findOrFail($id);

        $request->validate([
            'kelas_id'   => 'sometimes|required|exists:kelas,id',
            'mapel_id'   => 'sometimes|required|exists:mapel,id',
            'guru_id'    => 'sometimes|required|exists:users,id',
            'hari'       => ['sometimes','required', Rule::in(['senin','selasa','rabu','kamis','jumat'])],
            'jam_mulai'  => 'sometimes|required|date_format:H:i',
            'jam_selesai'=> 'sometimes|required|date_format:H:i|after:jam_mulai',
        ]);

        $jadwal->update($request->only([
            'kelas_id','mapel_id','guru_id','hari','jam_mulai','jam_selesai'
        ]));

        return response()->json([
            'message' => 'Jadwal berhasil diperbarui',
            'data'    => $this->format($jadwal->fresh()->load(['kelas','mapel','guru'])),
        ]);
    }

    // DELETE /api/jadwal/{id}
    public function destroy($id)
    {
        if (!$this->authorizeAdmin()) return response()->json(['message' => 'Unauthorized'], 403);

        Jadwal::findOrFail($id)->delete();

        return response()->json(['message' => 'Jadwal berhasil dihapus']);
    }

    private function format(Jadwal $j): array
    {
        return [
            'id'          => $j->id,
            'kelas'       => $j->kelas?->nama_kelas,
            'mapel'       => $j->mapel?->nama_mapel,
            'kode_mapel'  => $j->mapel?->kode_mapel,
            'guru'        => $j->guru?->name,
            'hari'        => $j->hari,
            'jam_mulai'   => $j->jam_mulai,
            'jam_selesai' => $j->jam_selesai,
            'kelas_id'    => $j->kelas_id,
            'mapel_id'    => $j->mapel_id,
            'guru_id'     => $j->guru_id,
        ];
    }
}
