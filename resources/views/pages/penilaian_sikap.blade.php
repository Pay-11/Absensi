@extends('layouts.app')
@section('title', 'Data Penilaian Sikap')

@section('content')

<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
id="layout-navbar">
    <div class="layout-navbar container-xxl">
        <h4 class="fw-bold">Data Rekap Penilaian Sikap</h4>
    </div>
</nav>

<div class="card mb-4 mt-4">
    <div class="card-header d-flex justify-content-between">
        <h5>Riwayat Penilaian (App Ionic)</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Siswa (NISN)</th>
                    <th>Dinilai Oleh</th>
                    <th>Predikat Sikap</th>
                    <th>Keterangan / Feedback</th>
                    <th>Tanggal Penilaian</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penilaians as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <span class="fw-bold">{{ $row->siswa->name ?? '-' }}</span><br>
                        <small class="text-muted">{{ $row->siswa->nisn ?? '-' }}</small>
                    </td>
                    <td>{{ $row->guru->name ?? 'Guru / Admin' }}</td>
                    <td>
                        @php
                            $badgeClass = 'bg-secondary';
                            switch(strtolower($row->sikap)) {
                                case 'sangat baik': $badgeClass = 'bg-success'; break;
                                case 'baik': $badgeClass = 'bg-primary'; break;
                                case 'cukup': $badgeClass = 'bg-warning text-dark'; break;
                                case 'kurang': $badgeClass = 'bg-danger'; break;
                                case 'sangat kurang': $badgeClass = 'bg-dark'; break;
                            }
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $row->sikap }}</span>
                    </td>
                    <td style="max-width: 200px; white-space: normal;">
                        {{ $row->keterangan ?? '-' }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">Belum ada data Penilaian Sikap!</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
