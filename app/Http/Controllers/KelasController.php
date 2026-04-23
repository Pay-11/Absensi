<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\TahunAjar;
use App\Models\User;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas     = Kelas::with(['tahunAjar','waliGuru'])->orderBy('nama_kelas')->get();
        $tahunAjar = TahunAjar::orderByDesc('aktif')->orderByDesc('id')->get();
        $guru      = User::where('role','guru')->orderBy('name')->get();

        return view('pages.kelas.index', compact('kelas','tahunAjar','guru'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas'    => 'required|string|max:100',
            'tahun_ajar_id' => 'required|exists:tahun_ajar,id',
            'wali_guru_id'  => 'nullable|exists:users,id',
        ]);

        Kelas::create([
            'nama_kelas'    => $request->nama_kelas,
            'tahun_ajar_id' => $request->tahun_ajar_id,
            'wali_guru_id'  => $request->wali_guru_id ?: null,
        ]);

        return redirect()->route('kelas.index')
            ->with('success', "Kelas {$request->nama_kelas} berhasil ditambahkan");
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $request->validate([
            'nama_kelas'    => 'required|string|max:100',
            'tahun_ajar_id' => 'required|exists:tahun_ajar,id',
            'wali_guru_id'  => 'nullable|exists:users,id',
        ]);

        $kelas->update([
            'nama_kelas'    => $request->nama_kelas,
            'tahun_ajar_id' => $request->tahun_ajar_id,
            'wali_guru_id'  => $request->wali_guru_id ?: null,
        ]);

        return redirect()->route('kelas.index')
            ->with('success', "Kelas {$kelas->nama_kelas} berhasil diperbarui");
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $nama  = $kelas->nama_kelas;
        $kelas->delete();

        return redirect()->route('kelas.index')
            ->with('success', "Kelas {$nama} berhasil dihapus");
    }
}
