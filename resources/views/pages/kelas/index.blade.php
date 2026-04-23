@extends('layouts.admin')

@section('title', 'Data Kelas')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">
            <i class="bx bx-chalkboard me-2 text-primary"></i> Data Kelas
        </h1>
        <button class="btn btn-primary" onclick="openCreateModal()">
            <i class="bx bx-plus me-1"></i> Tambah Kelas
        </button>
    </div>

    {{-- Info tahun ajar aktif --}}
    @php $aktif = $tahunAjar->firstWhere('aktif', true); @endphp
    @if($aktif)
    <div class="alert alert-success alert-dismissible fade show py-2 mb-3">
        <i class="bx bx-calendar-check me-1"></i>
        Tahun ajar aktif: <strong>{{ $aktif->nama }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="input-group" style="max-width:400px">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bx bx-search text-muted"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0"
                       placeholder="Cari nama kelas...">
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelKelas">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Nama Kelas</th>
                            <th>Tahun Ajaran</th>
                            <th>Wali Kelas</th>
                            <th>Dibuat</th>
                            <th class="text-center" width="140">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kelas as $index => $k)
                        <tr data-nama="{{ strtolower($k->nama_kelas) }}">
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-initial rounded-circle bg-label-primary"
                                         style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;">
                                        {{ strtoupper(substr($k->nama_kelas,0,2)) }}
                                    </div>
                                    <span class="fw-semibold">{{ $k->nama_kelas }}</span>
                                </div>
                            </td>
                            <td>
                                @if($k->tahunAjar)
                                    <span class="badge {{ $k->tahunAjar->aktif ? 'bg-success' : 'bg-label-secondary' }}">
                                        {{ $k->tahunAjar->nama }}
                                        @if($k->tahunAjar->aktif) <i class="bx bx-check ms-1"></i> @endif
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                @if($k->waliGuru)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-initial rounded-circle bg-label-success"
                                             style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:600;flex-shrink:0;">
                                            {{ strtoupper(substr($k->waliGuru->name,0,1)) }}
                                        </div>
                                        <span class="small">{{ $k->waliGuru->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted small">Belum ditentukan</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $k->created_at?->format('d M Y') }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning"
                                        onclick="openEditModal(
                                            {{ $k->id }},
                                            '{{ addslashes($k->nama_kelas) }}',
                                            {{ $k->tahun_ajar_id }},
                                            {{ $k->wali_guru_id ?? 'null' }}
                                        )">
                                    <i class="bx bx-edit-alt"></i>
                                </button>
                                <form action="{{ route('kelas.destroy', $k->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus kelas {{ addslashes($k->nama_kelas) }}?\nSiswa dalam kelas ini akan terlepas.')">
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
                                <i class="bx bx-chalkboard fs-3 d-block mb-2"></i>
                                Belum ada data kelas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small">
            Total: <strong>{{ $kelas->count() }}</strong> kelas
        </div>
    </div>
</div>

{{-- ===== MODAL TAMBAH/EDIT ===== --}}
<div class="modal fade" id="kelasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="kelasForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">Tambah Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nama Kelas <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_kelas" id="field_nama"
                               class="form-control" placeholder="Contoh: X IPA 1" maxlength="100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Tahun Ajaran <span class="text-danger">*</span>
                        </label>
                        <select name="tahun_ajar_id" id="field_tahun" class="form-select" required>
                            <option value="">-- Pilih Tahun Ajar --</option>
                            @foreach($tahunAjar as $t)
                                <option value="{{ $t->id }}" {{ $t->aktif ? 'selected' : '' }}>
                                    {{ $t->nama }} {{ $t->aktif ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Wali Kelas <small class="text-muted">(opsional)</small>
                        </label>
                        <select name="wali_guru_id" id="field_guru" class="form-select">
                            <option value="">-- Pilih Guru --</option>
                            @foreach($guru as $g)
                                <option value="{{ $g->id }}">{{ $g->name }}</option>
                            @endforeach
                        </select>
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
    const BASE_URL = "{{ url('/admin/kelas') }}";
    let modalInstance;

    document.addEventListener('DOMContentLoaded', () => {
        modalInstance = new bootstrap.Modal(document.getElementById('kelasModal'));

        document.getElementById('searchInput').addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#tabelKelas tbody tr[data-nama]').forEach(row => {
                row.style.display = row.dataset.nama.includes(q) ? '' : 'none';
            });
        });
    });

    function openCreateModal() {
        document.getElementById('kelasForm').reset();
        document.getElementById('kelasForm').action = BASE_URL;
        document.getElementById('formMethod').value  = 'POST';
        document.getElementById('modalTitle').innerText = 'Tambah Kelas Baru';
        modalInstance.show();
    }

    function openEditModal(id, nama, tahunId, guruId) {
        document.getElementById('kelasForm').action   = BASE_URL + '/' + id;
        document.getElementById('formMethod').value   = 'PUT';
        document.getElementById('modalTitle').innerText = 'Edit Kelas';
        document.getElementById('field_nama').value   = nama;
        document.getElementById('field_tahun').value  = tahunId;
        document.getElementById('field_guru').value   = guruId || '';
        modalInstance.show();
    }
</script>
@endsection

@endsection
