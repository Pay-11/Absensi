<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JadwalController extends Controller
{
    private array $hariList = ['senin','selasa','rabu','kamis','jumat'];

    public function index(Request $request)
    {
        $jadwal = Jadwal::with(['kelas','mapel','guru'])
            ->when($request->kelas_id, fn($q) => $q->where('kelas_id', $request->kelas_id))
            ->when($request->hari,     fn($q) => $q->where('hari', $request->hari))
            ->orderByRaw("FIELD(hari,'senin','selasa','rabu','kamis','jumat')")
            ->orderBy('jam_mulai')
            ->get();

        $kelas = Kelas::orderBy('nama_kelas')->get();
        $mapel = Mapel::orderBy('nama_mapel')->get();
        $guru  = User::where('role','guru')->orderBy('name')->get();

        return view('pages.jadwal.index', compact('jadwal','kelas','mapel','guru'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id'    => 'required|exists:kelas,id',
            'mapel_id'    => 'required|exists:mapel,id',
            'guru_id'     => 'required|exists:users,id',
            'hari'        => ['required', Rule::in($this->hariList)],
            'jam_mulai'   => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        Jadwal::create($request->only([
            'kelas_id','mapel_id','guru_id','hari','jam_mulai','jam_selesai'
        ]));

        return redirect()->route('jadwal.index')
            ->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);

        $request->validate([
            'kelas_id'    => 'required|exists:kelas,id',
            'mapel_id'    => 'required|exists:mapel,id',
            'guru_id'     => 'required|exists:users,id',
            'hari'        => ['required', Rule::in($this->hariList)],
            'jam_mulai'   => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $jadwal->update($request->only([
            'kelas_id','mapel_id','guru_id','hari','jam_mulai','jam_selesai'
        ]));

        return redirect()->route('jadwal.index')
            ->with('success', 'Jadwal berhasil diperbarui');
    }

    public function destroy($id)
    {
        Jadwal::findOrFail($id)->delete();

        return redirect()->route('jadwal.index')
            ->with('success', 'Jadwal berhasil dihapus');
    }
}
