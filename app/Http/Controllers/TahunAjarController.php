<?php

namespace App\Http\Controllers;

use App\Models\TahunAjar;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TahunAjarController extends Controller
{
    public function index()
    {
        $tahunAjar = TahunAjar::orderByDesc('aktif')->orderByDesc('id')->get();
        return view('pages.tahun-ajar.index', compact('tahunAjar'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:20|unique:tahun_ajar,nama',
        ]);

        // Jika set aktif, nonaktifkan semua dulu
        if ($request->boolean('aktif')) {
            TahunAjar::query()->update(['aktif' => false]);
        }

        TahunAjar::create([
            'nama'  => $request->nama,
            'aktif' => $request->boolean('aktif', false),
        ]);

        return redirect()->route('tahun-ajar.index')
            ->with('success', "Tahun ajar {$request->nama} berhasil ditambahkan");
    }

    public function update(Request $request, $id)
    {
        $tahunAjar = TahunAjar::findOrFail($id);

        $request->validate([
            'nama' => ['required', 'string', 'max:20', Rule::unique('tahun_ajar', 'nama')->ignore($id)],
        ]);

        if ($request->boolean('aktif')) {
            TahunAjar::where('id', '!=', $id)->update(['aktif' => false]);
        }

        $tahunAjar->update([
            'nama'  => $request->nama,
            'aktif' => $request->boolean('aktif'),
        ]);

        return redirect()->route('tahun-ajar.index')
            ->with('success', "Tahun ajar {$tahunAjar->nama} berhasil diperbarui");
    }

    public function destroy($id)
    {
        $tahunAjar = TahunAjar::findOrFail($id);

        if ($tahunAjar->aktif) {
            return redirect()->route('tahun-ajar.index')
                ->with('error', 'Tidak bisa menghapus tahun ajar yang sedang aktif!');
        }

        $tahunAjar->delete();

        return redirect()->route('tahun-ajar.index')
            ->with('success', 'Tahun ajar berhasil dihapus');
    }

    public function aktifkan($id)
    {
        TahunAjar::query()->update(['aktif' => false]);
        $tahunAjar = TahunAjar::findOrFail($id);
        $tahunAjar->update(['aktif' => true]);

        return redirect()->route('tahun-ajar.index')
            ->with('success', "Tahun ajar {$tahunAjar->nama} sekarang aktif");
    }
}
