<form action="{{ route('siswa.update', $siswa->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Nama</label>
    <input type="text" name="name" value="{{ $siswa->name }}">

    <label>NISN</label>
    <input type="text" name="nisn" value="{{ $siswa->nisn }}">

    <button type="submit">Update</button>
</form>