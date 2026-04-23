@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold text-gray-800">
            <i class="bx bx-user-circle me-2 text-primary"></i> Data Siswa
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('siswa.export') }}" class="btn btn-success">
                <i class="bx bx-download me-1"></i> Export Excel
            </a>
            <button class="btn btn-primary" onclick="openCreateModal()">
                <i class="bx bx-plus me-1"></i> Tambah Siswa
            </button>
        </div>
    </div>

    {{-- Alert sukses --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Search --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body py-2">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bx bx-search text-muted"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0"
                       placeholder="Cari nama, email, atau NISN...">
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelSiswa">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>NISN</th>
                            <th>Kelas</th>
                            <th class="text-center" width="160">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswa as $index => $s)
                        <tr data-name="{{ strtolower($s->name) }}"
                            data-email="{{ strtolower($s->email) }}"
                            data-nisn="{{ $s->nisn }}">
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-initial rounded-circle bg-label-primary"
                                         style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;font-weight:600">
                                        {{ strtoupper(substr($s->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-semibold">{{ $s->name }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $s->email }}</td>
                            <td><span class="badge bg-label-secondary">{{ $s->nisn ?? '-' }}</span></td>
                            <td>
                                @forelse($s->kelas as $k)
                                    <span class="badge bg-label-info me-1">{{ $k->nama_kelas }}</span>
                                @empty
                                    <span class="text-muted small">Belum ada kelas</span>
                                @endforelse
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning"
                                        onclick="openEditModal({{ $s->id }}, '{{ addslashes($s->name) }}', '{{ $s->email }}', '{{ $s->nisn }}')">
                                    <i class="bx bx-edit-alt"></i>
                                </button>
                                <form action="{{ route('siswa.destroy', $s->id) }}" method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Yakin hapus siswa {{ addslashes($s->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bx bx-user-x fs-3 d-block mb-2"></i>
                                Belum ada data siswa
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small">
            Total: <strong>{{ $siswa->count() }}</strong> siswa
        </div>
    </div>
</div>

{{-- ===== MODAL TAMBAH/EDIT ===== --}}
<div class="modal fade" id="siswaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="siswaForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">Tambah Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="field_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="field_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">NISN <span class="text-danger">*</span></label>
                        <input type="text" name="nisn" id="field_nisn" class="form-control" maxlength="20" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Password
                            <small class="text-muted" id="passwordHint">(wajib diisi)</small>
                        </label>
                        <div class="input-group">
                            <input type="password" name="password" id="field_password" class="form-control"
                                   placeholder="Min. 6 karakter">
                            <button class="btn btn-outline-secondary" type="button"
                                    onclick="togglePassword()">
                                <i class="bx bx-hide" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpan">
                        <i class="bx bx-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const BASE_URL = "{{ url('/admin/siswa') }}";
    let modalInstance;

    document.addEventListener('DOMContentLoaded', () => {
        modalInstance = new bootstrap.Modal(document.getElementById('siswaModal'));

        // Search / filter realtime
        document.getElementById('searchInput').addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#tabelSiswa tbody tr[data-name]').forEach(row => {
                const match = row.dataset.name.includes(q)
                           || row.dataset.email.includes(q)
                           || (row.dataset.nisn || '').includes(q);
                row.style.display = match ? '' : 'none';
            });
        });
    });

    function openCreateModal() {
        document.getElementById('siswaForm').reset();
        document.getElementById('siswaForm').action = BASE_URL;
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('modalTitle').innerText = 'Tambah Siswa Baru';
        document.getElementById('passwordHint').innerText = '(wajib diisi)';
        document.getElementById('field_password').required = true;
        modalInstance.show();
    }

    function openEditModal(id, name, email, nisn) {
        document.getElementById('siswaForm').action = BASE_URL + '/' + id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('modalTitle').innerText = 'Edit Data Siswa';
        document.getElementById('passwordHint').innerText = '(kosongkan jika tidak diubah)';
        document.getElementById('field_password').required = false;
        document.getElementById('field_name').value  = name;
        document.getElementById('field_email').value = email;
        document.getElementById('field_nisn').value  = nisn;
        document.getElementById('field_password').value = '';
        modalInstance.show();
    }

    function togglePassword() {
        const input = document.getElementById('field_password');
        const icon  = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bx bx-show';
        } else {
            input.type = 'password';
            icon.className = 'bx bx-hide';
        }
    }
</script>
@endsection
