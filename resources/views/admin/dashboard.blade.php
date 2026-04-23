@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">

    {{-- Greeting --}}
    <div class="mb-4">
        <h4 class="fw-bold mb-0">Dashboard 👋</h4>
        <p class="text-muted">Selamat datang di panel manajemen AbsensiApp.</p>
    </div>

    {{-- ===== STAT CARDS ===== --}}
    <div class="row g-3 mb-4">

        {{-- Total Siswa --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-label-primary"
                         style="width:52px;height:52px;flex-shrink:0;">
                        <i class="bx bx-user-circle fs-3 text-primary"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Siswa</div>
                        <div class="fw-bold fs-4">{{ $totalSiswa }}</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-2 px-3">
                    <a href="{{ route('siswa.index') }}" class="small text-primary">
                        Lihat semua <i class="bx bx-right-arrow-alt"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Total Guru --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-label-success"
                         style="width:52px;height:52px;flex-shrink:0;">
                        <i class="bx bx-id-card fs-3 text-success"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Guru</div>
                        <div class="fw-bold fs-4">{{ $totalGuru }}</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-2 px-3">
                    <a href="{{ route('guru.admin.index') }}" class="small text-success">
                        Lihat semua <i class="bx bx-right-arrow-alt"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Total Kelas --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-label-warning"
                         style="width:52px;height:52px;flex-shrink:0;">
                        <i class="bx bx-chalkboard fs-3 text-warning"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Kelas</div>
                        <div class="fw-bold fs-4">{{ $totalKelas }}</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-2 px-3">
                    <span class="small text-muted">Tahun ajaran aktif</span>
                </div>
            </div>
        </div>

        {{-- Absensi Hari Ini --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-label-info"
                         style="width:52px;height:52px;flex-shrink:0;">
                        <i class="bx bx-check-square fs-3 text-info"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Absensi Hari Ini</div>
                        <div class="fw-bold fs-4">{{ $totalAbsensiHariIni }}</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-2 px-3">
                    <span class="small text-muted">{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
            </div>
        </div>

    </div>

    {{-- ===== ROW 2: Shortcuts + Mapel ===== --}}
    <div class="row g-3">

        {{-- Quick Actions --}}
        <div class="col-xl-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom fw-semibold">
                    <i class="bx bx-zap me-1 text-primary"></i> Akses Cepat
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('siswa.index') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="bx bx-user-circle fs-4 d-block mb-1"></i>
                                <small>Data Siswa</small>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('guru.admin.index') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="bx bx-id-card fs-4 d-block mb-1"></i>
                                <small>Data Guru</small>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('point-rules.index') }}" class="btn btn-outline-warning w-100 py-3">
                                <i class="bx bx-list-ol fs-4 d-block mb-1"></i>
                                <small>Aturan Poin</small>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('flexibility-items.index') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="bx bx-store-alt fs-4 d-block mb-1"></i>
                                <small>Item Penukaran</small>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-danger w-100 py-3">
                                <i class="bx bx-shield-quarter fs-4 d-block mb-1"></i>
                                <small>Kelola Admin</small>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="btn btn-outline-secondary w-100 py-3">
                                <i class="bx bx-time-five fs-4 d-block mb-1"></i>
                                <small>Jadwal</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Mapel --}}
        <div class="col-xl-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                    <span class="fw-semibold"><i class="bx bx-book-open me-1 text-primary"></i> Ringkasan Mata Pelajaran</span>
                    <span class="badge bg-label-primary">{{ $totalMapel }} Mapel</span>
                </div>
                <div class="card-body">
                    <div class="row text-center g-3">
                        <div class="col-4">
                            <div class="border rounded p-3">
                                <div class="fw-bold fs-3 text-primary">{{ $totalSiswa }}</div>
                                <div class="text-muted small">Siswa Aktif</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-3">
                                <div class="fw-bold fs-3 text-success">{{ $totalGuru }}</div>
                                <div class="text-muted small">Guru Aktif</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-3">
                                <div class="fw-bold fs-3 text-warning">{{ $totalMapel }}</div>
                                <div class="text-muted small">Mata Pelajaran</div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bx bx-check-circle text-success fs-5"></i>
                            <span class="small">Absensi tercatat hari ini</span>
                        </div>
                        <span class="badge bg-success">{{ $totalAbsensiHariIni }} catatan</span>
                    </div>
                </div>
                <div class="card-footer bg-transparent text-muted small">
                    Data diperbarui: {{ now()->format('d M Y, H:i') }} WIB
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
