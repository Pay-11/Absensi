@extends('layouts.admin')

@section('title', 'Data Guru')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">
            <i class="bx bx-id-card me-2 text-primary"></i> Data Guru
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('guru.export') }}" class="btn btn-success">
                <i class="bx bx-download me-1"></i> Export Excel
            </a>
            <button class="btn btn-primary" onclick="openCreateModal()">
                <i class="bx bx-plus me-1"></i> Tambah Guru
            </button>
        </div>
    </div>

    {{-- Search --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body py-2">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bx bx-search text-muted"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0"
                       placeholder="Cari nama, email, atau NIP...">
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelGuru">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>NIP</th>
                            <th>Mata Pelajaran</th>
                            <th class="text-center" width="160">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($guru as $index => $g)
                        <tr data-name="{{ strtolower($g->name) }}"
                            data-email="{{ strtolower($g->email) }}"
                            data-nip="{{ $g->nip }}">
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-initial rounded-circle bg-label-success"
                                         style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;font-weight:600;flex-shrink:0;">
                                        {{ strtoupper(substr($g->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-semibold">{{ $g->name }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $g->email }}</td>
                            <td>
                                @if($g->nip)
                                    <span class="badge bg-label-secondary">{{ $g->nip }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                @forelse($g->mapel as $m)
                                    <span class="badge bg-label-warning me-1">{{ $m->nama_mapel }}</span>
                                @empty
                                    <span class="text-muted small">Belum ada mapel</span>
                                @endforelse
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning"
                                        onclick="openEditModal({{ $g->id }}, '{{ addslashes($g->name) }}', '{{ $g->email }}', '{{ $g->nip }}')">
                                    <i class="bx bx-edit-alt"></i>
                                </button>
                                <form action="{{ route('guru.admin.destroy', $g->id) }}" method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Yakin hapus guru {{ addslashes($g->name) }}?')">
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
                                Belum ada data guru
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small">
            Total: <strong>{{ $guru->count() }}</strong> guru
        </div>
    </div>
</div>

{{-- ===== MODAL TAMBAH/EDIT ===== --}}
<div class="modal fade" id="guruModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="guruForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">Tambah Guru</h5>
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
                        <label class="form-label fw-semibold">NIP <small class="text-muted">(opsional)</small></label>
                        <input type="text" name="nip" id="field_nip" class="form-control" maxlength="20"
                               placeholder="Nomor Induk Pegawai">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Password <span class="text-danger" id="passRequired">*</span>
                            <small class="text-muted" id="passwordHint"></small>
                        </label>
                        <div class="input-group">
                            <input type="password" name="password" id="field_password" class="form-control"
                                   placeholder="Min. 6 karakter">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                <i class="bx bx-hide" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
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
    const BASE_URL = "{{ url('/admin/guru') }}";
    let modalInstance;

    document.addEventListener('DOMContentLoaded', () => {
        modalInstance = new bootstrap.Modal(document.getElementById('guruModal'));

        // Search realtime
        document.getElementById('searchInput').addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#tabelGuru tbody tr[data-name]').forEach(row => {
                const match = row.dataset.name.includes(q)
                           || row.dataset.email.includes(q)
                           || (row.dataset.nip || '').includes(q);
                row.style.display = match ? '' : 'none';
            });
        });
    });

    function openCreateModal() {
        document.getElementById('guruForm').reset();
        document.getElementById('guruForm').action = BASE_URL;
        document.getElementById('formMethod').value  = 'POST';
        document.getElementById('modalTitle').innerText = 'Tambah Guru Baru';
        document.getElementById('passwordHint').innerText = '';
        document.getElementById('passRequired').style.display = 'inline';
        document.getElementById('field_password').required = true;
        modalInstance.show();
    }

    function openEditModal(id, name, email, nip) {
        document.getElementById('guruForm').action = BASE_URL + '/' + id;
        document.getElementById('formMethod').value  = 'PUT';
        document.getElementById('modalTitle').innerText = 'Edit Data Guru';
        document.getElementById('passwordHint').innerText = '(kosongkan jika tidak diubah)';
        document.getElementById('passRequired').style.display = 'none';
        document.getElementById('field_password').required = false;

        document.getElementById('field_name').value  = name;
        document.getElementById('field_email').value = email;
        document.getElementById('field_nip').value   = nip !== 'null' ? nip : '';
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
