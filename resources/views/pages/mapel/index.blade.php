@extends('layouts.admin')

@section('title', 'Mata Pelajaran')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">
            <i class="bx bx-book-open me-2 text-primary"></i> Mata Pelajaran
        </h1>
        <button class="btn btn-primary" onclick="openCreateModal()">
            <i class="bx bx-plus me-1"></i> Tambah Mapel
        </button>
    </div>

    {{-- Search --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bx bx-search text-muted"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0"
                       placeholder="Cari nama atau kode mapel...">
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelMapel">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Kode Mapel</th>
                            <th>Nama Mata Pelajaran</th>
                            <th>Dibuat</th>
                            <th class="text-center" width="140">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mapel as $index => $m)
                        <tr data-nama="{{ strtolower($m->nama_mapel) }}"
                            data-kode="{{ strtolower($m->kode_mapel) }}">
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td>
                                <span class="badge bg-label-primary fw-semibold" style="font-size:.8rem">
                                    {{ $m->kode_mapel }}
                                </span>
                            </td>
                            <td class="fw-semibold">{{ $m->nama_mapel }}</td>
                            <td class="text-muted small">{{ $m->created_at?->format('d M Y') }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning"
                                        onclick="openEditModal({{ $m->id }}, '{{ addslashes($m->nama_mapel) }}', '{{ $m->kode_mapel }}')">
                                    <i class="bx bx-edit-alt"></i>
                                </button>
                                <form action="{{ route('mapel.destroy', $m->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus mapel {{ addslashes($m->nama_mapel) }}?')">
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
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bx bx-book-x fs-3 d-block mb-2"></i>
                                Belum ada mata pelajaran
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small">
            Total: <strong>{{ $mapel->count() }}</strong> mata pelajaran
        </div>
    </div>
</div>

{{-- ===== MODAL TAMBAH/EDIT ===== --}}
<div class="modal fade" id="mapelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="mapelForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">Tambah Mata Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Kode Mapel <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="kode_mapel" id="field_kode"
                               class="form-control text-uppercase"
                               placeholder="Contoh: MTK, IPA, BIG" maxlength="20" required
                               oninput="this.value = this.value.toUpperCase()">
                        <div class="form-text">Kode unik untuk mata pelajaran</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nama Mata Pelajaran <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_mapel" id="field_nama"
                               class="form-control"
                               placeholder="Contoh: Matematika" maxlength="100" required>
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
    const BASE_URL = "{{ url('/admin/mapel') }}";
    let modalInstance;

    document.addEventListener('DOMContentLoaded', () => {
        modalInstance = new bootstrap.Modal(document.getElementById('mapelModal'));

        document.getElementById('searchInput').addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#tabelMapel tbody tr[data-nama]').forEach(row => {
                const match = row.dataset.nama.includes(q) || row.dataset.kode.includes(q);
                row.style.display = match ? '' : 'none';
            });
        });
    });

    function openCreateModal() {
        document.getElementById('mapelForm').reset();
        document.getElementById('mapelForm').action = BASE_URL;
        document.getElementById('formMethod').value  = 'POST';
        document.getElementById('modalTitle').innerText = 'Tambah Mata Pelajaran';
        modalInstance.show();
    }

    function openEditModal(id, nama, kode) {
        document.getElementById('mapelForm').action   = BASE_URL + '/' + id;
        document.getElementById('formMethod').value   = 'PUT';
        document.getElementById('modalTitle').innerText = 'Edit Mata Pelajaran';
        document.getElementById('field_nama').value   = nama;
        document.getElementById('field_kode').value   = kode;
        modalInstance.show();
    }
</script>
@endsection

@endsection
