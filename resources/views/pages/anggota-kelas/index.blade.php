@extends('layouts.admin')

@section('title', 'Anggota Kelas')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold text-gray-800">
            <i class="bx bx-group me-2 text-primary"></i> Anggota Kelas
        </h1>
        @if(isset($selectedKelas) && $selectedKelas)
        <a href="{{ route('anggota-kelas.export', ['kelas_id' => $selectedKelas->id]) }}"
           class="btn btn-success">
            <i class="bx bx-download me-1"></i> Export Excel
        </a>
        @endif
    </div>

    {{-- Pilih Kelas --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('anggota-kelas.index') }}" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Pilih Kelas</label>
                    <select name="kelas_id" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}"
                                {{ optional($selectedKelas)->id == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                                @if($k->tahunAjar) ({{ $k->tahunAjar->nama }}) @endif
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

    @if($selectedKelas)

    {{-- Info Kelas --}}
    <div class="card border-0 bg-label-primary mb-3">
        <div class="card-body py-2 d-flex gap-4 flex-wrap">
            <span><i class="bx bx-chalkboard me-1"></i> <strong>{{ $selectedKelas->nama_kelas }}</strong></span>
            @if($selectedKelas->tahunAjar)
                <span><i class="bx bx-calendar me-1"></i> {{ $selectedKelas->tahunAjar->nama }}</span>
            @endif
            @if($selectedKelas->waliGuru)
                <span><i class="bx bx-user me-1"></i> Wali Kelas: {{ $selectedKelas->waliGuru->name }}</span>
            @endif
            <span class="ms-auto"><strong>{{ $anggota->count() }}</strong> siswa terdaftar</span>
        </div>
    </div>

    <div class="row g-4">

        {{-- ===== TABEL ANGGOTA ===== --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Daftar Siswa</span>
                    <input type="text" id="searchAnggota" class="form-control form-control-sm w-auto"
                           placeholder="Cari siswa...">
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tabelAnggota">
                            <thead class="table-light">
                                <tr>
                                    <th width="45">#</th>
                                    <th>Nama Siswa</th>
                                    <th>Email</th>
                                    <th>NISN</th>
                                    <th class="text-center" width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($anggota as $i => $ag)
                                <tr data-nama="{{ strtolower($ag->murid->name) }}">
                                    <td class="text-muted">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-initial rounded-circle bg-label-success"
                                                 style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;font-weight:600">
                                                {{ strtoupper(substr($ag->murid->name, 0, 1)) }}
                                            </div>
                                            <span class="fw-semibold">{{ $ag->murid->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $ag->murid->email }}</td>
                                    <td><span class="badge bg-label-secondary">{{ $ag->murid->nisn ?? '-' }}</span></td>
                                    <td class="text-center">
                                        {{-- Pindah Kelas --}}
                                        <button class="btn btn-sm btn-warning me-1"
                                                onclick="openPindahModal({{ $ag->id }}, '{{ addslashes($ag->murid->name) }}')"
                                                title="Pindah Kelas">
                                            <i class="bx bx-transfer-alt"></i>
                                        </button>
                                        {{-- Hapus dari kelas --}}
                                        <form action="{{ route('anggota-kelas.destroy', $ag->id) }}" method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Keluarkan {{ addslashes($ag->murid->name) }} dari kelas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" title="Keluarkan dari kelas">
                                                <i class="bx bx-x"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bx bx-user-x fs-3 d-block mb-2"></i>
                                        Belum ada siswa di kelas ini
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== FORM TAMBAH SISWA ===== --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-semibold">
                    <i class="bx bx-user-plus me-1 text-primary"></i> Tambah Siswa ke Kelas
                </div>
                <div class="card-body">
                    {{-- Catatan aturan --}}
                    <div class="alert alert-warning py-2 small mb-3">
                        <i class="bx bx-info-circle me-1"></i>
                        <strong>Aturan:</strong> Setiap siswa hanya bisa terdaftar di <strong>satu kelas</strong>.
                        Siswa yang sudah memiliki kelas tidak akan ditampilkan di sini.
                    </div>

                    @if($siswaOptions->isEmpty())
                        <div class="alert alert-secondary py-2 small mb-0">
                            <i class="bx bx-user-x me-1"></i>
                            Tidak ada siswa yang belum memiliki kelas.
                            Tambah siswa baru di menu <strong>Data Siswa</strong>, atau gunakan
                            fitur <strong>Pindah Kelas</strong> untuk memindahkan siswa antar kelas.
                        </div>
                    @else
                    <form action="{{ route('anggota-kelas.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kelas_id" value="{{ $selectedKelas->id }}">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Pilih Siswa <span class="text-danger">*</span>
                                <small class="text-muted">(bisa pilih banyak)</small>
                            </label>
                            <select name="murid_ids[]" id="selectSiswa" class="form-select" multiple
                                    style="height: 220px;">
                                @foreach($siswaOptions as $s)
                                    <option value="{{ $s->id }}">
                                        {{ $s->name }}
                                        @if($s->nisn) ({{ $s->nisn }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Tahan Ctrl / Cmd untuk pilih banyak</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                    onclick="selectAllSiswa()">Pilih Semua</button>
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bx bx-plus me-1"></i> Tambahkan
                            </button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>

    </div> {{-- /row --}}
    @endif

</div>

{{-- ===== MODAL PINDAH KELAS ===== --}}
<div class="modal fade" id="pindahModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form id="pindahForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Pindah Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2 small">Pindahkan <strong id="namaTarget"></strong> ke:</p>
                    <select name="kelas_tujuan_id" class="form-select" required>
                        @foreach($kelasList as $k)
                            @if(optional($selectedKelas)->id != $k->id)
                            <option value="{{ $k->id }}">
                                {{ $k->nama_kelas }}
                                @if($k->tahunAjar) ({{ $k->tahunAjar->nama }}) @endif
                            </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm">Pindahkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Search realtime
    document.getElementById('searchAnggota')?.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#tabelAnggota tbody tr[data-nama]').forEach(row => {
            row.style.display = row.dataset.nama.includes(q) ? '' : 'none';
        });
    });

    // Modal pindah kelas
    let pindahModal;
    document.addEventListener('DOMContentLoaded', () => {
        pindahModal = new bootstrap.Modal(document.getElementById('pindahModal'));
    });

    function openPindahModal(anggotaId, nama) {
        document.getElementById('namaTarget').innerText = nama;
        document.getElementById('pindahForm').action = '/admin/anggota-kelas/' + anggotaId;
        pindahModal.show();
    }

    function selectAllSiswa() {
        const sel = document.getElementById('selectSiswa');
        for (let opt of sel.options) opt.selected = true;
    }
</script>
@endsection
