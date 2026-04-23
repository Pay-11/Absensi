@extends('layouts.admin')

@section('title', 'Rekap Absensi')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">
                <i class="bx bx-check-square me-2 text-primary"></i> Rekap Absensi
            </h1>
            <p class="text-muted small mb-0 mt-1">Rekap kehadiran siswa per kelas dan periode</p>
        </div>
        @if(isset($kelasId) && $kelasId && isset($sesiList) && $sesiList->count() > 0)
        <a href="{{ route('absensi.rekap.export', array_filter(['kelas_id' => $kelasId, 'mapel_id' => $mapelId ?? null, 'tgl_mulai' => $tglMulai, 'tgl_selesai' => $tglSelesai])) }}"
           class="btn btn-success">
            <i class="bx bx-download me-1"></i> Export Excel
        </a>
        @endif
    </div>

    {{-- Filter Form --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent fw-semibold border-bottom">
            <i class="bx bx-filter-alt me-1 text-primary"></i> Filter Data
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('absensi.rekap') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
                    <select name="kelas_id" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                                @if($k->tahunAjar) ({{ $k->tahunAjar->nama }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Mata Pelajaran</label>
                    <select name="mapel_id" class="form-select">
                        <option value="">Semua Mapel</option>
                        @foreach($mapel as $m)
                            <option value="{{ $m->id }}" {{ $mapelId == $m->id ? 'selected' : '' }}>
                                {{ $m->kode_mapel }} – {{ $m->nama_mapel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Dari Tanggal</label>
                    <input type="date" name="tgl_mulai" class="form-control" value="{{ $tglMulai }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Sampai Tanggal</label>
                    <input type="date" name="tgl_selesai" class="form-control" value="{{ $tglSelesai }}">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-search me-1"></i> Tampilkan
                    </button>
                    @if($kelasId)
                    <a href="{{ route('absensi.rekap') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-x"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($kelasId && $sesiList->count() > 0)

    {{-- Summary Stats --}}
    @php
        $totalHadir = $rekap->sum('hadir');
        $totalIzin  = $rekap->sum('izin');
        $totalAlpha = $rekap->sum('alpha');
        $totalAll   = $totalHadir + $totalIzin + $totalAlpha;
        $pctHadir   = $totalAll > 0 ? round($totalHadir / $totalAll * 100) : 0;
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-sm-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="fw-bold fs-2 text-success">{{ $totalHadir }}</div>
                    <div class="text-muted small">Total Hadir</div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="fw-bold fs-2 text-warning">{{ $totalIzin }}</div>
                    <div class="text-muted small">Total Izin</div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="fw-bold fs-2 text-danger">{{ $totalAlpha }}</div>
                    <div class="text-muted small">Total Alpha</div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="fw-bold fs-2 text-primary">{{ $pctHadir }}%</div>
                    <div class="text-muted small">Tingkat Kehadiran</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Rekap --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
            <span class="fw-semibold">
                Rekap Kehadiran · {{ $sesiList->count() }} Pertemuan
            </span>
            <div class="d-flex gap-2">
                <span class="badge bg-success px-2">H = Hadir</span>
                <span class="badge bg-warning text-dark px-2">I = Izin</span>
                <span class="badge bg-danger px-2">A = Alpha</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0 small">
                    <thead class="table-light text-center">
                        <tr>
                            <th class="text-start" style="min-width:180px">Nama Siswa</th>
                            @foreach($sesiList as $sesi)
                            <th style="min-width:54px; font-size:11px;">
                                <div>{{ \Carbon\Carbon::parse($sesi->tanggal)->format('d/m') }}</div>
                                <div class="text-muted" style="font-size:10px">
                                    {{ $sesi->jadwal?->mapel?->kode_mapel ?? '-' }}
                                </div>
                                <a href="{{ route('absensi.detail', $sesi->id) }}"
                                   class="text-decoration-none" title="Lihat detail sesi">
                                    <i class="bx bx-link-external" style="font-size:10px"></i>
                                </a>
                            </th>
                            @endforeach
                            <th class="text-success" style="min-width:50px">H</th>
                            <th class="text-warning" style="min-width:50px">I</th>
                            <th class="text-danger" style="min-width:50px">A</th>
                            <th style="min-width:60px">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekap as $row)
                        <tr>
                            <td class="fw-semibold">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-initial rounded-circle bg-label-primary"
                                         style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:600;flex-shrink:0;">
                                        {{ strtoupper(substr($row['siswa']->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div>{{ $row['siswa']->name }}</div>
                                        @if($row['siswa']->nisn)
                                            <div class="text-muted" style="font-size:10px">{{ $row['siswa']->nisn }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            @foreach($sesiList as $sesi)
                            @php $status = $row['detail'][$sesi->id] ?? 'alpha'; @endphp
                            <td class="text-center
                                {{ $status === 'hadir' ? 'bg-success bg-opacity-10 text-success' : '' }}
                                {{ $status === 'izin'  ? 'bg-warning bg-opacity-10 text-warning' : '' }}
                                {{ $status === 'alpha' ? 'bg-danger bg-opacity-10 text-danger'   : '' }}
                                fw-bold">
                                {{ strtoupper(substr($status, 0, 1)) }}
                            </td>
                            @endforeach
                            <td class="text-center fw-bold text-success">{{ $row['hadir'] }}</td>
                            <td class="text-center fw-bold text-warning">{{ $row['izin'] }}</td>
                            <td class="text-center fw-bold text-danger">{{ $row['alpha'] }}</td>
                            <td class="text-center fw-bold">
                                @php
                                    $total = $row['hadir'] + $row['izin'] + $row['alpha'];
                                    $pct   = $total > 0 ? round($row['hadir'] / $total * 100) : 0;
                                @endphp
                                <span class="badge {{ $pct >= 75 ? 'bg-success' : ($pct >= 50 ? 'bg-warning' : 'bg-danger') }}">
                                    {{ $pct }}%
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small">
            {{ $rekap->count() }} siswa · Periode {{ \Carbon\Carbon::parse($tglMulai)->format('d M Y') }}
            s/d {{ \Carbon\Carbon::parse($tglSelesai)->format('d M Y') }}
        </div>
    </div>

    @elseif($kelasId && $sesiList->count() === 0)
    <div class="alert alert-info">
        <i class="bx bx-info-circle me-2"></i>
        Tidak ada sesi absensi pada rentang tanggal yang dipilih.
    </div>
    @else
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5 text-muted">
            <i class="bx bx-select-multiple fs-1 d-block mb-3"></i>
            <div class="fw-semibold">Pilih kelas dan periode untuk menampilkan rekap</div>
            <div class="small mt-1">Gunakan filter di atas lalu klik <strong>Tampilkan</strong></div>
        </div>
    </div>
    @endif

</div>
@endsection
