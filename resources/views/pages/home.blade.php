@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
id="layout-navbar">

<div class="layout-navbar container-xxl">
    <h4 class="fw-bold">Dashboard Admin</h4>
</div>

</nav>



<!-- SISWA -->
<div class="card mb-4">
<div class="card-header d-flex justify-content-between">
<h5>Data Siswa</h5>
<a href="{{ route('siswa.create') }}" class="btn btn-primary">+ Tambah Siswa</a>
</div>

<div class="table-responsive">
<table class="table">
<thead>
<tr>
<th>Nama</th>
<th>NIS</th>
<th>Kelas</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
@foreach($siswa as $s)
<tr>
<td>{{ $s->name }}</td>
<td>{{ $s->nisn }}</td>
<td>{{ $s->anggotaKelas?->first()?->kelas?->nama_kelas ?? '-' }}</td>
<td>
<a href="{{ route('siswa.edit',$s->id) }}" class="btn btn-warning btn-sm">Edit</a>

<form action="{{ route('siswa.destroy',$s->id) }}" method="POST" style="display:inline;">
@csrf
@method('DELETE')
<button class="btn btn-danger btn-sm">Hapus</button>
</form>

</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>


<!-- KELAS -->
<div class="card">
<div class="card-header d-flex justify-content-between">
<h5>Data Kelas</h5>
<a href="#" class="btn btn-primary">+ Tambah Kelas</a>
</div>

<div class="table-responsive">
<table class="table">
<thead>
<tr>
<th>Nama Kelas</th>
<th>Wali Kelas</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>
@foreach($kelas as $k)
<tr>
<td>{{ $k->nama_kelas }}</td>
<td>{{ $k->waliGuru?->name ?? '-' }}</td>
<td>

<a href="#" class="btn btn-warning btn-sm">Edit</a>

<form action="#" method="POST" style="display:inline;">
@csrf
@method('DELETE')
<button class="btn btn-danger btn-sm">Hapus</button>
</form>

</td>
</tr>
@endforeach
</tbody>

</table>
</div>
</div>

</div>
</div>

@endsection 