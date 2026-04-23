@extends('layouts.admin')

@section('title', 'Kategori Assessment')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">
            <i class="bx bx-category me-2 text-primary"></i> Kategori Assessment
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('assessment-categories.export') }}" class="btn btn-success">
                <i class="bx bx-download me-1"></i> Export Excel
            </a>
            <button class="btn btn-primary" onclick="openCreateModal()">
                <i class="bx bx-plus me-1"></i> Tambah Kategori
            </button>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-primary">{{ $categories->count() }}</div>
                <div class="small text-muted">Total Kategori</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-success">{{ $categories->where('is_active', true)->count() }}</div>
                <div class="small text-muted">Aktif</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-secondary">{{ $categories->where('is_active', false)->count() }}</div>
                <div class="small text-muted">Nonaktif</div>
            </div>
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
                       placeholder="Cari nama kategori...">
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelKategori">
                    <thead class="table-light">
                        <tr>
                            <th width="45">#</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th class="text-center" width="100">Status</th>
                            <th class="text-center" width="160">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $i => $cat)
                        <tr data-nama="{{ strtolower($cat->name) }}">
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-initial rounded-circle {{ $cat->is_active ? 'bg-label-primary' : 'bg-label-secondary' }}"
                                         style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;font-weight:700;">
                                        {{ strtoupper(substr($cat->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-semibold {{ !$cat->is_active ? 'text-muted' : '' }}">
                                        {{ $cat->name }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-muted small">
                                {{ $cat->description ?? '-' }}
                            </td>
                            <td class="text-center">
                                <form action="{{ route('assessment-categories.toggle', $cat->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit"
                                            class="badge border-0 {{ $cat->is_active ? 'bg-success' : 'bg-secondary' }}"
                                            style="cursor:pointer;padding:6px 10px;"
                                            title="{{ $cat->is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}">
                                        {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning me-1"
                                        onclick="openEditModal({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ addslashes($cat->description ?? '') }}')"
                                        title="Edit">
                                    <i class="bx bx-edit-alt"></i>
                                </button>
                                <form action="{{ route('assessment-categories.destroy', $cat->id) }}" method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Yakin hapus kategori {{ addslashes($cat->name) }}?\nKategori yang sudah dipakai tidak dapat dihapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bx bx-category fs-2 d-block mb-2"></i>
                                Belum ada kategori assessment
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small">
            Total: <strong>{{ $categories->count() }}</strong> kategori
        </div>
    </div>
</div>

{{-- ===== MODAL TAMBAH/EDIT ===== --}}
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="categoryForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nama Kategori <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="field_name" class="form-control"
                               placeholder="contoh: Kedisiplinan, Keaktifan..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Deskripsi <small class="text-muted">(opsional)</small>
                        </label>
                        <textarea name="description" id="field_description" class="form-control"
                                  rows="3" placeholder="Deskripsi singkat kategori ini..."></textarea>
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
    const BASE_URL = "{{ route('assessment-categories.index') }}";
    let modalInstance;

    document.addEventListener('DOMContentLoaded', () => {
        modalInstance = new bootstrap.Modal(document.getElementById('categoryModal'));

        document.getElementById('searchInput').addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#tabelKategori tbody tr[data-nama]').forEach(row => {
                row.style.display = row.dataset.nama.includes(q) ? '' : 'none';
            });
        });
    });

    function openCreateModal() {
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryForm').action = BASE_URL;
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('modalTitle').innerText = 'Tambah Kategori Baru';
        modalInstance.show();
    }

    function openEditModal(id, name, description) {
        document.getElementById('categoryForm').action = BASE_URL + '/' + id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('modalTitle').innerText = 'Edit Kategori';
        document.getElementById('field_name').value = name;
        document.getElementById('field_description').value = description;
        modalInstance.show();
    }
</script>
@endsection

@endsection
