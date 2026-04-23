@extends('layouts.admin')

@section('title', 'Detail Sesi Absensi')

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="{{ route('absensi.rekap') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> Kembali
        </a>
        <h1 class="h4 fw-bold mb-0">Detail Sesi Absensi</h1>
    </div>

    {{-- Info Sesi --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3 text-center">
                    <div class="text-muted small">Tanggal</div>
                    <div class="fw-bold fs-5">
                        {{ \Carbon\Carbon::parse($sesi->tanggal)->translatedFormat('d F Y') }}
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="text-muted small">Kelas</div>
                    <div class="fw-bold fs-5">{{ $sesi->jadwal?->kelas?->nama_kelas ?? '-' }}</div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="text-muted small">Mata Pelajaran</div>
                    <div class="fw-bold fs-5">{{ $sesi->jadwal?->mapel?->nama_mapel ?? '-' }}</div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="text-muted small">Guru</div>
                    <div class="fw-bold fs-5">{{ $sesi->jadwal?->guru?->name ?? '-' }}</div>
                </div>
            </div>

            <hr class="my-3">

            {{-- Summary badge --}}
            @php
                $hCount = $absensi->where('status','hadir')->count();
                $iCount = $absensi->where('status','izin')->count();
                $aCount = $absensi->where('status','alpha')->count();
                $total  = $absensi->count();
            @endphp
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <div class="text-center px-4 py-2 rounded bg-success bg-opacity-10">
                    <div class="fw-bold fs-4 text-success">{{ $hCount }}</div>
                    <div class="small text-success">Hadir</div>
                </div>
                <div class="text-center px-4 py-2 rounded bg-warning bg-opacity-10">
                    <div class="fw-bold fs-4 text-warning">{{ $iCount }}</div>
                    <div class="small text-warning">Izin</div>
                </div>
                <div class="text-center px-4 py-2 rounded bg-danger bg-opacity-10">
                    <div class="fw-bold fs-4 text-danger">{{ $aCount }}</div>
                    <div class="small text-danger">Alpha</div>
                </div>
                <div class="text-center px-4 py-2 rounded bg-primary bg-opacity-10">
                    <div class="fw-bold fs-4 text-primary">{{ $total }}</div>
                    <div class="small text-primary">Total</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Detail --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Nama Siswa</th>
                            <th>NISN</th>
                            <th>Status</th>
                            <th>Waktu Scan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensi as $index => $a)
                        <tr>
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-initial rounded-circle bg-label-primary"
                                         style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:12px;flex-shrink:0;">
                                        {{ strtoupper(substr($a->murid?->name ?? '?', 0, 1)) }}
                                    </div>
                                    <span class="fw-semibold">{{ $a->murid?->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="text-muted small">{{ $a->murid?->nisn ?? '-' }}</td>
                            <td>
                                @if($a->status === 'hadir')
                                    <span class="badge bg-success"><i class="bx bx-check me-1"></i>Hadir</span>
                                @elseif($a->status === 'izin')
                                    <span class="badge bg-warning text-dark"><i class="bx bx-time me-1"></i>Izin</span>
                                @else
                                    <span class="badge bg-danger"><i class="bx bx-x me-1"></i>Alpha</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $a->waktu_scan ? \Carbon\Carbon::parse($a->waktu_scan)->format('H:i:s') : '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bx bx-user-x fs-3 d-block mb-2"></i>
                                Belum ada data absensi untuk sesi ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small">
            Total: <strong>{{ $total }}</strong> siswa tercatat
        </div>
    </div>

</div>
@endsection
