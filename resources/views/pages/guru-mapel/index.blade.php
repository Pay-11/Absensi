@extends('layouts.admin')

@section('title', 'Guru Mata Pelajaran')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">
            <i class="bx bx-book-bookmark me-2 text-primary"></i> Penugasan Guru Mapel
        </h1>
        @if($selectedGuru)
        <a href="{{ route('guru-mapel.export', ['guru_id' => $selectedGuru->id]) }}"
           class="btn btn-success">
            <i class="bx bx-download me-1"></i> Export Excel
        </a>
        @else
        <a href="{{ route('guru-mapel.export') }}" class="btn btn-success">
            <i class="bx bx-download me-1"></i> Export Semua
        </a>
        @endif
    </div>

    {{-- Pilih Guru --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('guru-mapel.index') }}" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Pilih Guru</label>
                    <select name="guru_id" class="form-select" required>
                        <option value="">-- Pilih Guru --</option>
                        @foreach($guruList as $g)
                            <option value="{{ $g->id }}"
                                {{ optional($selectedGuru)->id == $g->id ? 'selected' : '' }}>
                                {{ $g->name }}
                                @if($g->nip) (NIP: {{ $g->nip }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-filter-alt me-1"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($selectedGuru)

    {{-- Info Guru --}}
    <div class="card border-0 bg-label-primary mb-3">
        <div class="card-body py-2 d-flex gap-4 flex-wrap align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar-initial rounded-circle bg-primary text-white"
                     style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;">
                    {{ strtoupper(substr($selectedGuru->name, 0, 1)) }}
                </div>
                <div>
                    <div class="fw-bold">{{ $selectedGuru->name }}</div>
                    <div class="small text-muted">{{ $selectedGuru->email }}</div>
                </div>
            </div>
            @if($selectedGuru->nip)
                <span><i class="bx bx-id-card me-1"></i> NIP: {{ $selectedGuru->nip }}</span>
            @endif
            <span class="ms-auto">
                <strong>{{ $mapelDiajar->count() }}</strong> mata pelajaran ditugaskan
            </span>
        </div>
    </div>

    <div class="row g-4">

        {{-- ===== TABEL MAPEL YANG DIAJARKAN ===== --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Mata Pelajaran yang Diajarkan</span>
                    <input type="text" id="searchMapel" class="form-control form-control-sm w-auto"
                           placeholder="Cari mapel...">
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tabelMapel">
                            <thead class="table-light">
                                <tr>
                                    <th width="45">#</th>
                                    <th>Nama Mata Pelajaran</th>
                                    <th>Kode</th>
                                    <th class="text-center" width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mapelDiajar as $i => $m)
                                <tr data-nama="{{ strtolower($m->nama_mapel) }}">
                                    <td class="text-muted">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-initial rounded-circle bg-label-warning"
                                                 style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;font-weight:600;">
                                                {{ strtoupper(substr($m->nama_mapel, 0, 1)) }}
                                            </div>
                                            <span class="fw-semibold">{{ $m->nama_mapel }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($m->kode_mapel)
                                            <span class="badge bg-label-secondary">{{ $m->kode_mapel }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{-- Hapus penugasan --}}
                                        <form action="{{ route('guru-mapel.destroy') }}" method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Hapus penugasan {{ addslashes($m->nama_mapel) }} dari guru ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="guru_id"  value="{{ $selectedGuru->id }}">
                                            <input type="hidden" name="mapel_id" value="{{ $m->id }}">
                                            <button class="btn btn-sm btn-danger" title="Hapus penugasan">
                                                <i class="bx bx-x"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bx bx-book-open fs-3 d-block mb-2"></i>
                                        Guru ini belum ditugaskan ke mata pelajaran apapun
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== FORM TUGASKAN MAPEL ===== --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-semibold">
                    <i class="bx bx-plus-circle me-1 text-primary"></i> Tugaskan Mata Pelajaran
                </div>
                <div class="card-body">
                    @if($mapelOptions->isEmpty())
                        <div class="alert alert-secondary py-2 small mb-0">
                            <i class="bx bx-info-circle me-1"></i>
                            Semua mata pelajaran sudah ditugaskan ke guru ini,
                            atau belum ada data mata pelajaran.
                            Tambah mapel baru di menu <strong>Mata Pelajaran</strong>.
                        </div>
                    @else
                    <form action="{{ route('guru-mapel.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="guru_id" value="{{ $selectedGuru->id }}">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Pilih Mata Pelajaran <span class="text-danger">*</span>
                                <small class="text-muted">(bisa pilih banyak)</small>
                            </label>
                            <select name="mapel_ids[]" id="selectMapel" class="form-select" multiple
                                    style="height: 200px;">
                                @foreach($mapelOptions as $mp)
                                    <option value="{{ $mp->id }}">
                                        {{ $mp->nama_mapel }}
                                        @if($mp->kode_mapel) ({{ $mp->kode_mapel }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Tahan Ctrl / Cmd untuk pilih banyak</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                    onclick="selectAllMapel()">Pilih Semua</button>
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bx bx-check me-1"></i> Tugaskan
                            </button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Ringkasan semua guru dan mapelnya --}}
            <div class="card shadow-sm border-0 mt-3">
                <div class="card-header bg-white fw-semibold small">
                    <i class="bx bx-table me-1 text-muted"></i> Ringkasan Semua Guru
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($guruList as $gl)
                        <li class="list-group-item d-flex justify-content-between align-items-start py-2
                                   {{ $gl->id == $selectedGuru->id ? 'bg-label-primary' : '' }}">
                            <div class="small">
                                <div class="fw-semibold">{{ $gl->name }}</div>
                            </div>
                            <span class="badge bg-label-warning rounded-pill">
                                {{ $gl->mapel->count() }} mapel
                            </span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

    </div>{{-- /row --}}
    @endif

</div>

<script>
    document.getElementById('searchMapel')?.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#tabelMapel tbody tr[data-nama]').forEach(row => {
            row.style.display = row.dataset.nama.includes(q) ? '' : 'none';
        });
    });

    function selectAllMapel() {
        const sel = document.getElementById('selectMapel');
        if (sel) for (let opt of sel.options) opt.selected = true;
    }
</script>
@endsection
