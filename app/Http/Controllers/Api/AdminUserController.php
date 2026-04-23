<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AdminUserController extends Controller
{
    /**
     * Hanya superadmin yang boleh manage akun admin.
     */
    private function authorizeSuperAdmin()
    {
        $user = JWTAuth::parseToken()->authenticate();

        if ($user->role !== 'superadmin') {
            return null;
        }

        return $user;
    }

    // ============================
    // GET ALL ADMIN (with pagination & search)
    // ============================
    public function index(Request $request)
    {
        $auth = $this->authorizeSuperAdmin();

        if (!$auth) {
            return response()->json(['message' => 'Unauthorized. Hanya superadmin yang bisa mengakses.'], 403);
        }

        $query = User::where('role', 'admin');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $admins  = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'message' => 'Data admin berhasil diambil',
            'data'    => $admins->map(fn($a) => $this->format($a)),
            'meta'    => [
                'current_page' => $admins->currentPage(),
                'last_page'    => $admins->lastPage(),
                'per_page'     => $admins->perPage(),
                'total'        => $admins->total(),
            ],
        ]);
    }

    // ============================
    // GET SINGLE ADMIN
    // ============================
    public function show($id)
    {
        if (!$this->authorizeSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $admin = User::where('role', 'admin')->findOrFail($id);

        return response()->json([
            'message' => 'Data admin ditemukan',
            'data'    => $this->format($admin),
        ]);
    }

    // ============================
    // CREATE ADMIN
    // ============================
    public function store(Request $request)
    {
        if (!$this->authorizeSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $admin = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'admin',
        ]);

        return response()->json([
            'message' => 'Admin berhasil ditambahkan',
            'data'    => $this->format($admin),
        ], 201);
    }

    // ============================
    // UPDATE ADMIN
    // ============================
    public function update(Request $request, $id)
    {
        if (!$this->authorizeSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $admin = User::where('role', 'admin')->findOrFail($id);

        $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'email'    => ['sometimes', 'required', 'email', Rule::unique('users', 'email')->ignore($admin->id)],
            'password' => 'sometimes|nullable|string|min:6',
        ]);

        $data = $request->only(['name', 'email']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return response()->json([
            'message' => 'Data admin berhasil diperbarui',
            'data'    => $this->format($admin->fresh()),
        ]);
    }

    // ============================
    // DELETE ADMIN
    // ============================
    public function destroy($id)
    {
        if (!$this->authorizeSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $admin = User::where('role', 'admin')->findOrFail($id);
        $admin->delete();

        return response()->json(['message' => 'Admin berhasil dihapus']);
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
            'role'       => $user->role,
            'created_at' => $user->created_at?->toDateTimeString(),
            'updated_at' => $user->updated_at?->toDateTimeString(),
        ];
    }
}
