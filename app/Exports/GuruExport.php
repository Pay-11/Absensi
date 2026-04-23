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

class GuruExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents, ShouldAutoSize
{
    use HasTableStyle;

    protected function headerColor(): string { return '1a3a7a'; }
    protected function stripeColor(): string  { return 'EEF2FA'; }

    public function collection()
    {
        return User::where('role', 'guru')->with('mapel')->orderBy('name')->get();
    }

    public function headings(): array
    {
        return ['#', 'Nama Lengkap', 'Email', 'NIP', 'Mata Pelajaran', 'Terdaftar Sejak'];
    }

    private int $rowIndex = 0;

    public function map($row): array
    {
        $this->rowIndex++;
        return [
            $this->rowIndex,
            $row->name,
            $row->email,
            $row->nip ?? '-',
            $row->mapel->pluck('nama_mapel')->join(', ') ?: 'Belum ditugaskan',
            $row->created_at->format('d/m/Y'),
        ];
    }
}
