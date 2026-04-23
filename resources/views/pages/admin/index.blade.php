@extends('layouts.admin')

@section('title', 'Kelola Admin')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">
                <i class="bx bx-shield-quarter me-2 text-primary"></i> Kelola Akun Admin
            </h1>
            <p class="text-muted small mb-0 mt-1">Hanya superadmin yang dapat mengelola akun ini</p>
        </div>
        <button class="btn btn-primary" onclick="openCreateModal()">
            <i class="bx bx-plus me-1"></i> Tambah Admin
        </button>
    </div>

    {{-- Search --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body py-2">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bx bx-search text-muted"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0"
                       placeholder="Cari nama atau email...">
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelAdmin">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Dibuat</th>
                            <th class="text-center" width="160">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $index => $a)
                        <tr data-name="{{ strtolower($a->name) }}"
                            data-email="{{ strtolower($a->email) }}">
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-initial rounded-circle bg-label-danger"
                                         style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;font-weight:600;flex-shrink:0;">
                                        {{ strtoupper(substr($a->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-semibold">{{ $a->name }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $a->email }}</td>
                            <td>
                                <span class="badge bg-label-danger">
                                    <i class="bx bx-shield me-1"></i> Admin
                                </span>
                            </td>
                            <td class="text-muted small">
                                {{ $a->created_at?->format('d M Y') }}
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning"
                                        onclick="openEditModal({{ $a->id }}, '{{ addslashes($a->name) }}', '{{ $a->email }}')">
                                    <i class="bx bx-edit-alt"></i>
                                </button>
                                <form action="{{ route('admin.accounts.destroy', $a->id) }}" method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Yakin hapus admin {{ addslashes($a->name) }}?\nAksi ini tidak bisa dibatalkan!')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bx bx-user-x fs-3 d-block mb-2"></i>
                                Belum ada akun admin
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small">
            Total: <strong>{{ $admins->count() }}</strong> admin
        </div>
    </div>
</div>

{{-- ===== MODAL TAMBAH/EDIT ===== --}}
<div class="modal fade" id="adminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="adminForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">Tambah Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    {{-- Banner peringatan --}}
                    <div class="alert alert-warning py-2 small mb-3">
                        <i class="bx bx-error me-1"></i>
                        Akun admin memiliki akses penuh ke panel. Pastikan data sudah benar.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="field_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="field_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Password
                            <span class="text-danger" id="passRequired">*</span>
                            <small class="text-muted" id="passwordHint"></small>
                        </label>
                        <div class="input-group">
                            <input type="password" name="password" id="field_password"
                                   class="form-control" placeholder="Min. 6 karakter">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                <i class="bx bx-hide" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    const BASE_URL = "{{ url('/admin/accounts') }}";
    let modalInstance;

    document.addEventListener('DOMContentLoaded', () => {
        modalInstance = new bootstrap.Modal(document.getElementById('adminModal'));

        // Search realtime
        document.getElementById('searchInput').addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#tabelAdmin tbody tr[data-name]').forEach(row => {
                const match = row.dataset.name.includes(q)
                           || row.dataset.email.includes(q);
                row.style.display = match ? '' : 'none';
            });
        });
    });

    function openCreateModal() {
        document.getElementById('adminForm').reset();
        document.getElementById('adminForm').action = BASE_URL;
        document.getElementById('formMethod').value  = 'POST';
        document.getElementById('modalTitle').innerText  = 'Tambah Admin Baru';
        document.getElementById('passwordHint').innerText = '';
        document.getElementById('passRequired').style.display = 'inline';
        document.getElementById('field_password').required = true;
        modalInstance.show();
    }

    function openEditModal(id, name, email) {
        document.getElementById('adminForm').action = BASE_URL + '/' + id;
        document.getElementById('formMethod').value  = 'PUT';
        document.getElementById('modalTitle').innerText  = 'Edit Akun Admin';
        document.getElementById('passwordHint').innerText = '(kosongkan jika tidak diubah)';
        document.getElementById('passRequired').style.display = 'none';
        document.getElementById('field_password').required = false;

        document.getElementById('field_name').value  = name;
        document.getElementById('field_email').value = email;
        document.getElementById('field_password').value = '';
        modalInstance.show();
    }

    function togglePassword() {
        const input = document.getElementById('field_password');
        const icon  = document.getElementById('eyeIcon');
        input.type  = input.type === 'password' ? 'text' : 'password';
        icon.className = input.type === 'text' ? 'bx bx-show' : 'bx bx-hide';
    }
</script>
@endsection

@endsection
