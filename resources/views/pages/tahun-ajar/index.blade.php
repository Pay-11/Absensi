@extends('layouts.admin')

@section('title', 'Tahun Ajaran')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">
                <i class="bx bx-calendar me-2 text-primary"></i> Tahun Ajaran
            </h1>
            <p class="text-muted small mb-0 mt-1">Hanya satu tahun ajar yang boleh aktif</p>
        </div>
        <button class="btn btn-primary" onclick="openCreateModal()">
            <i class="bx bx-plus me-1"></i> Tambah Tahun Ajar
        </button>
    </div>

    {{-- Tabel --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Nama Tahun Ajaran</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th class="text-center" width="200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tahunAjar as $index => $t)
                        <tr class="{{ $t->aktif ? 'table-success bg-opacity-25' : '' }}">
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bx bx-calendar {{ $t->aktif ? 'text-success' : 'text-muted' }} fs-5"></i>
                                    <span class="fw-semibold">{{ $t->nama }}</span>
                                </div>
                            </td>
                            <td>
                                @if($t->aktif)
                                    <span class="badge bg-success">
                                        <i class="bx bx-check me-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $t->created_at?->format('d M Y') }}
                            </td>
                            <td class="text-center">
                                {{-- Tombol Aktifkan (hanya kalau belum aktif) --}}
                                @if(!$t->aktif)
                                <form action="{{ route('tahun-ajar.aktifkan', $t->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Aktifkan tahun ajar {{ $t->nama }}?')">
                                    @csrf
                                    <button class="btn btn-sm btn-success" type="submit" title="Aktifkan">
                                        <i class="bx bx-power-off"></i>
                                    </button>
                                </form>
                                @else
                                <button class="btn btn-sm btn-success disabled" title="Sudah aktif">
                                    <i class="bx bx-power-off"></i>
                                </button>
                                @endif

                                {{-- Tombol Edit --}}
                                <button class="btn btn-sm btn-warning"
                                        onclick="openEditModal({{ $t->id }}, '{{ $t->nama }}', {{ $t->aktif ? 'true' : 'false' }})"
                                        title="Edit">
                                    <i class="bx bx-edit-alt"></i>
                                </button>

                                {{-- Tombol Hapus (disabled kalau aktif) --}}
                                @if(!$t->aktif)
                                <form action="{{ route('tahun-ajar.destroy', $t->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus tahun ajar {{ $t->nama }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit" title="Hapus">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                                @else
                                <button class="btn btn-sm btn-danger disabled" title="Tidak bisa hapus yang aktif">
                                    <i class="bx bx-trash"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bx bx-calendar-x fs-3 d-block mb-2"></i>
                                Belum ada tahun ajaran
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small">
            Total: <strong>{{ $tahunAjar->count() }}</strong> tahun ajar &nbsp;|&nbsp;
            Aktif: <strong class="text-success">{{ $tahunAjar->where('aktif', true)->count() }}</strong>
        </div>
    </div>

</div>

{{-- ===== MODAL TAMBAH/EDIT ===== --}}
<div class="modal fade" id="tahunAjarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="tahunAjarForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">Tambah Tahun Ajar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nama Tahun Ajaran <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama" id="field_nama" class="form-control"
                               placeholder="Contoh: 2025/2026" maxlength="20" required>
                        <div class="form-text">Format: YYYY/YYYY (contoh: 2025/2026)</div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="aktif"
                                   id="field_aktif" value="1">
                            <label class="form-check-label fw-semibold" for="field_aktif">
                                Jadikan tahun ajar aktif
                            </label>
                        </div>
                        <div class="form-text text-warning">
                            <i class="bx bx-info-circle me-1"></i>
                            Mengaktifkan ini akan menonaktifkan tahun ajar lain
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
    const BASE_URL = "{{ url('/admin/tahun-ajar') }}";
    let modalInstance;

    document.addEventListener('DOMContentLoaded', () => {
        modalInstance = new bootstrap.Modal(document.getElementById('tahunAjarModal'));
    });

    function openCreateModal() {
        document.getElementById('tahunAjarForm').reset();
        document.getElementById('tahunAjarForm').action = BASE_URL;
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('modalTitle').innerText = 'Tambah Tahun Ajar Baru';
        document.getElementById('field_aktif').checked = false;
        modalInstance.show();
    }

    function openEditModal(id, nama, aktif) {
        document.getElementById('tahunAjarForm').action = BASE_URL + '/' + id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('modalTitle').innerText = 'Edit Tahun Ajar';
        document.getElementById('field_nama').value = nama;
        document.getElementById('field_aktif').checked = aktif;
        modalInstance.show();
    }
</script>
@endsection

@endsection
