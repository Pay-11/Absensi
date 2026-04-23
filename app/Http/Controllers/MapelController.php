<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MapelController extends Controller
{
    public function index()
    {
        $mapel = Mapel::orderBy('nama_mapel')->get();
        return view('pages.mapel.index', compact('mapel'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:100',
            'kode_mapel' => 'required|string|max:20|unique:mapel,kode_mapel',
        ]);

        Mapel::create($request->only(['nama_mapel', 'kode_mapel']));

        return redirect()->route('mapel.index')
            ->with('success', "Mapel {$request->nama_mapel} berhasil ditambahkan");
    }

    public function update(Request $request, $id)
    {
        $mapel = Mapel::findOrFail($id);

        $request->validate([
            'nama_mapel' => 'required|string|max:100',
            'kode_mapel' => ['required', 'string', 'max:20', Rule::unique('mapel', 'kode_mapel')->ignore($id)],
        ]);

        $mapel->update($request->only(['nama_mapel', 'kode_mapel']));

        return redirect()->route('mapel.index')
            ->with('success', "Mapel {$mapel->nama_mapel} berhasil diperbarui");
    }

    public function destroy($id)
    {
        $mapel = Mapel::findOrFail($id);
        $nama  = $mapel->nama_mapel;
        $mapel->delete();

        return redirect()->route('mapel.index')
            ->with('success', "Mapel {$nama} berhasil dihapus");
    }
}
