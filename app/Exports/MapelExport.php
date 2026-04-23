<?php

namespace App\Exports;

use App\Models\Mapel;
use App\Exports\Concerns\HasTableStyle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MapelExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents, ShouldAutoSize
{
    use HasTableStyle;

    protected function headerColor(): string { return '7a4a1a'; }
    protected function stripeColor(): string  { return 'FAF3EE'; }

    public function collection()
    {
        return Mapel::orderBy('nama_mapel')->get();
    }

    public function headings(): array
    {
        return ['#', 'Nama Mata Pelajaran', 'Kode Mapel', 'Dibuat'];
    }

    private int $rowIndex = 0;

    public function map($row): array
    {
        $this->rowIndex++;
        return [
            $this->rowIndex,
            $row->nama_mapel,
            $row->kode_mapel ?? '-',
            $row->created_at->format('d/m/Y'),
        ];
    }
}
