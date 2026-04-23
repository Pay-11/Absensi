<?php

namespace App\Exports;

use App\Models\User;
use App\Exports\Concerns\HasTableStyle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents, ShouldAutoSize
{
    use HasTableStyle;

    public function collection()
    {
        return User::where('role', 'murid')->with('kelas')->orderBy('name')->get();
    }

    public function headings(): array
    {
        return ['#', 'Nama Lengkap', 'Email', 'NISN', 'Kelas', 'Terdaftar Sejak'];
    }

    private int $rowIndex = 0;

    public function map($row): array
    {
        $this->rowIndex++;
        return [
            $this->rowIndex,
            $row->name,
            $row->email,
            $row->nisn ?? '-',
            $row->kelas->pluck('nama_kelas')->join(', ') ?: 'Belum ada kelas',
            $row->created_at->format('d/m/Y'),
        ];
    }
}
