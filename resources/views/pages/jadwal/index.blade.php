@extends('layouts.admin')

@section('title', 'Jadwal Pelajaran')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">
            <i class="bx bx-time-five me-2 text-primary"></i> Jadwal Pelajaran
        </h1>
        <button class="btn btn-primary" onclick="openCreateModal()">
            <i class="bx bx-plus me-1"></i> Tambah Jadwal
        </button>
    </div>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('jadwal.index') }}" class="row g-2 align-items-center">
                <div class="col-auto">
                    <select name="kelas_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <select name="hari" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Hari</option>
                        @foreach(['senin','selasa','rabu','kamis','jumat'] as $h)
                            <option value="{{ $h }}" {{ request('hari') == $h ? 'selected' : '' }}>
                                {{ ucfirst($h) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if(request('kelas_id') || request('hari'))
                <div class="col-auto">
                    <a href="{{ route('jadwal.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bx bx-x"></i> Reset
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Hari</th>
                            <th>Jam</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Guru</th>
                            <th class="text-center" width="130">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $hariColors = [
                                'senin'   => 'bg-label-primary',
                                'selasa'  => 'bg-label-success',
                                'rabu'    => 'bg-label-warning',
                                'kamis'   => 'bg-label-info',
                                'jumat'   => 'bg-label-danger',
                            ];
                        @endphp
                        @forelse($jadwal as $index => $j)
                        <tr>
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td>
                                <span class="badge {{ $hariColors[$j->hari] ?? 'bg-secondary' }} text-capitalize fw-semibold">
                                    {{ $j->hari }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold">{{ substr($j->jam_mulai, 0, 5) }}</span>
                                <span class="text-muted mx-1">–</span>
                                <span class="fw-semibold">{{ substr($j->jam_selesai, 0, 5) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary">{{ $j->kelas?->nama_kelas ?? '-' }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $j->mapel?->nama_mapel ?? '-' }}</div>
                                <div class="text-muted small">{{ $j->mapel?->kode_mapel }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-initial rounded-circle bg-label-success"
                                         style="width:30px;height:30px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;flex-shrink:0;">
                                        {{ strtoupper(substr($j->guru?->name ?? '?', 0, 1)) }}
                                    </div>
                                    <span class="small">{{ $j->guru?->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning"
                                        onclick="openEditModal(
                                            {{ $j->id }},
                                            {{ $j->kelas_id }},
                                            {{ $j->mapel_id }},
                                            {{ $j->guru_id }},
                                            '{{ $j->hari }}',
                                            '{{ substr($j->jam_mulai,0,5) }}',
                                            '{{ substr($j->jam_selesai,0,5) }}'
                                        )">
                                    <i class="bx bx-edit-alt"></i>
                                </button>
                                <form action="{{ route('jadwal.destroy', $j->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus jadwal ini?')">
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
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bx bx-calendar-x fs-3 d-block mb-2"></i>
                                Belum ada jadwal pelajaran
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small">
            Menampilkan <strong>{{ $jadwal->count() }}</strong> jadwal
        </div>
    </div>
</div>

{{-- ===== MODAL TAMBAH/EDIT ===== --}}
<div class="modal fade" id="jadwalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="jadwalForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">Tambah Jadwal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
                            <select name="kelas_id" id="field_kelas" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mata Pelajaran <span class="text-danger">*</span></label>
                            <select name="mapel_id" id="field_mapel" class="form-select" required>
                                <option value="">-- Pilih Mapel --</option>
                                @foreach($mapel as $m)
                                    <option value="{{ $m->id }}">{{ $m->kode_mapel }} – {{ $m->nama_mapel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Guru Pengajar <span class="text-danger">*</span></label>
                            <select name="guru_id" id="field_guru" class="form-select" required>
                                <option value="">-- Pilih Guru --</option>
                                @foreach($guru as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Hari <span class="text-danger">*</span></label>
                            <select name="hari" id="field_hari" class="form-select" required>
                                <option value="">-- Pilih Hari --</option>
                                @foreach(['senin','selasa','rabu','kamis','jumat'] as $h)
                                    <option value="{{ $h }}">{{ ucfirst($h) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_mulai" id="field_mulai"
                                   class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_selesai" id="field_selesai"
                                   class="form-control" required>
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
    const BASE_URL = "{{ url('/admin/jadwal') }}";
    let modalInstance;

    document.addEventListener('DOMContentLoaded', () => {
        modalInstance = new bootstrap.Modal(document.getElementById('jadwalModal'));
    });

    function openCreateModal() {
        document.getElementById('jadwalForm').reset();
        document.getElementById('jadwalForm').action = BASE_URL;
        document.getElementById('formMethod').value  = 'POST';
        document.getElementById('modalTitle').innerText = 'Tambah Jadwal Pelajaran';
        modalInstance.show();
    }

    function openEditModal(id, kelasId, mapelId, guruId, hari, jamMulai, jamSelesai) {
        document.getElementById('jadwalForm').action   = BASE_URL + '/' + id;
        document.getElementById('formMethod').value    = 'PUT';
        document.getElementById('modalTitle').innerText = 'Edit Jadwal Pelajaran';
        document.getElementById('field_kelas').value   = kelasId;
        document.getElementById('field_mapel').value   = mapelId;
        document.getElementById('field_guru').value    = guruId;
        document.getElementById('field_hari').value    = hari;
        document.getElementById('field_mulai').value   = jamMulai;
        document.getElementById('field_selesai').value = jamSelesai;
        modalInstance.show();
    }
</script>
@endsection

@endsection
