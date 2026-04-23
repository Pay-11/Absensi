<?php

namespace App\Exports;

use App\Exports\Concerns\HasTableStyle;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GuruMapelExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents, ShouldAutoSize
{
    use HasTableStyle;

    protected function headerColor(): string { return '3a5a1a'; }
    protected function stripeColor(): string  { return 'F2F7EE'; }

    protected $guruId;

    public function __construct($guruId = null)
    {
        $this->guruId = $guruId;
    }

    public function collection()
    {
        $query = DB::table('guru_mapel')
            ->join('users', 'users.id', '=', 'guru_mapel.guru_id')
            ->join('mapel', 'mapel.id', '=', 'guru_mapel.mapel_id')
            ->select('users.name as guru_name', 'users.nip', 'mapel.nama_mapel', 'mapel.kode_mapel', 'guru_mapel.created_at');

        if ($this->guruId) {
            $query->where('guru_mapel.guru_id', $this->guruId);
        }

        return $query->orderBy('users.name')->orderBy('mapel.nama_mapel')->get();
    }

    public function headings(): array
    {
        return ['#', 'Nama Guru', 'NIP', 'Mata Pelajaran', 'Kode Mapel', 'Ditugaskan'];
    }

    private int $rowIndex = 0;

    public function map($row): array
    {
        $this->rowIndex++;
        return [
            $this->rowIndex,
            $row->guru_name,
            $row->nip ?? '-',
            $row->nama_mapel,
            $row->kode_mapel ?? '-',
            $row->created_at,
        ];
    }
}
