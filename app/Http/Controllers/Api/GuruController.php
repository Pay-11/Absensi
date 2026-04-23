<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class GuruController extends Controller
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
    // GET ALL GURU (with pagination & search)
    // ============================
    public function index(Request $request)
    {
        $auth = $this->authorizeAdmin();

        if (!$auth) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = User::where('role', 'guru');

        // Pencarian berdasarkan nama, email, atau nip
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $guru    = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'message' => 'Data guru berhasil diambil',
            'data'    => $guru->map(fn($g) => $this->format($g)),
            'meta'    => [
                'current_page' => $guru->currentPage(),
                'last_page'    => $guru->lastPage(),
                'per_page'     => $guru->perPage(),
                'total'        => $guru->total(),
            ],
        ]);
    }

    // ============================
    // GET SINGLE GURU
    // ============================
    public function show($id)
    {
        $auth = $this->authorizeAdmin();

        if (!$auth) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $guru = User::where('role', 'guru')->findOrFail($id);

        return response()->json([
            'message' => 'Data guru ditemukan',
            'data'    => $this->format($guru),
        ]);
    }

    // ============================
    // CREATE GURU
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
            'nip'      => 'nullable|string|max:20|unique:users,nip',
        ]);

        $guru = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'guru',
            'nip'      => $request->nip,
        ]);

        return response()->json([
            'message' => 'Guru berhasil ditambahkan',
            'data'    => $this->format($guru),
        ], 201);
    }

    // ============================
    // UPDATE GURU
    // ============================
    public function update(Request $request, $id)
    {
        $auth = $this->authorizeAdmin();

        if (!$auth) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $guru = User::where('role', 'guru')->findOrFail($id);

        $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'email'    => ['sometimes', 'required', 'email', Rule::unique('users', 'email')->ignore($guru->id)],
            'password' => 'sometimes|nullable|string|min:6',
            'nip'      => ['sometimes', 'nullable', 'string', 'max:20', Rule::unique('users', 'nip')->ignore($guru->id)],
        ]);

        $data = $request->only(['name', 'email', 'nip']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $guru->update($data);

        return response()->json([
            'message' => 'Data guru berhasil diperbarui',
            'data'    => $this->format($guru->fresh()),
        ]);
    }

    // ============================
    // DELETE GURU
    // ============================
    public function destroy($id)
    {
        $auth = $this->authorizeAdmin();

        if (!$auth) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $guru = User::where('role', 'guru')->findOrFail($id);
        $guru->delete();

        return response()->json([
            'message' => 'Guru berhasil dihapus',
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
            'nip'        => $user->nip,
            'role'       => $user->role,
            'created_at' => $user->created_at?->toDateTimeString(),
            'updated_at' => $user->updated_at?->toDateTimeString(),
        ];
    }
}
