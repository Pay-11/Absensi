<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class SiswaController extends Controller
{
    /**
     * Pastikan yang mengakses adalah superadmin atau admin.
     */
    private function authorizeAdmin()
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!in_array($user->role, ['superadmin', 'admin'])) {
            return null;
        }

        return $user;
    }

    // ============================
    // GET ALL SISWA (with pagination & search)
    // ============================
    public function index(Request $request)
    {
        $auth = $this->authorizeAdmin();

        if (!$auth) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = User::where('role', 'murid');

        // Pencarian berdasarkan nama, email, atau nisn
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $siswa   = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'message' => 'Data siswa berhasil diambil',
            'data'    => $siswa->map(fn($s) => $this->format($s)),
            'meta'    => [
                'current_page' => $siswa->currentPage(),
                'last_page'    => $siswa->lastPage(),
                'per_page'     => $siswa->perPage(),
                'total'        => $siswa->total(),
            ],
        ]);
    }

    // ============================
    // GET SINGLE SISWA
    // ============================
    public function show($id)
    {
        $auth = $this->authorizeAdmin();

        if (!$auth) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $siswa = User::where('role', 'murid')->findOrFail($id);

        return response()->json([
            'message' => 'Data siswa ditemukan',
            'data'    => $this->format($siswa),
        ]);
    }

    // ============================
    // CREATE SISWA
    // ============================
    public function store(Request $request)
    {
        $auth = $this->authorizeAdmin();

        if (!$auth) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'nisn'     => 'required|string|max:20|unique:users,nisn',
        ]);

        $siswa = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'murid',
            'nisn'     => $request->nisn,
        ]);

        return response()->json([
            'message' => 'Siswa berhasil ditambahkan',
            'data'    => $this->format($siswa),
        ], 201);
    }

    // ============================
    // UPDATE SISWA
    // ============================
    public function update(Request $request, $id)
    {
        $auth = $this->authorizeAdmin();

        if (!$auth) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $siswa = User::where('role', 'murid')->findOrFail($id);

        $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'email'    => ['sometimes', 'required', 'email', Rule::unique('users', 'email')->ignore($siswa->id)],
            'password' => 'sometimes|nullable|string|min:6',
            'nisn'     => ['sometimes', 'required', 'string', 'max:20', Rule::unique('users', 'nisn')->ignore($siswa->id)],
        ]);

        $data = $request->only(['name', 'email', 'nisn']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $siswa->update($data);

        return response()->json([
            'message' => 'Data siswa berhasil diperbarui',
            'data'    => $this->format($siswa->fresh()),
        ]);
    }

    // ============================
    // DELETE SISWA
    // ============================
    public function destroy($id)
    {
        $auth = $this->authorizeAdmin();

        if (!$auth) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $siswa = User::where('role', 'murid')->findOrFail($id);
        $siswa->delete();

        return response()->json([
            'message' => 'Siswa berhasil dihapus',
        ]);
    }

    // ============================
    // HELPER: Format response
    // ============================
    private function format(User $user): array
    {
        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'nisn'       => $user->nisn,
            'role'       => $user->role,
            'created_at' => $user->created_at?->toDateTimeString(),
            'updated_at' => $user->updated_at?->toDateTimeString(),
        ];
    }
}
