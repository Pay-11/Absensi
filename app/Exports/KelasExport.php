<?php

namespace App\Exports;

use App\Models\Kelas;
use App\Exports\Concerns\HasTableStyle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KelasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents, ShouldAutoSize
{
    use HasTableStyle;

    protected function headerColor(): string { return '1a5a7a'; }
    protected function stripeColor(): string  { return 'EEF6FA'; }

    public function collection()
    {
        return Kelas::with(['tahunAjar', 'waliGuru'])->orderBy('nama_kelas')->get();
    }

    public function headings(): array
    {
        return ['#', 'Nama Kelas', 'Tahun Ajaran', 'Wali Kelas', 'Dibuat'];
    }

    private int $rowIndex = 0;

    public function map($row): array
    {
        $this->rowIndex++;
        return [
            $this->rowIndex,
            $row->nama_kelas,
            $row->tahunAjar->nama ?? '-',
            $row->waliGuru->name ?? '-',
            $row->created_at->format('d/m/Y'),
        ];
    }
}
