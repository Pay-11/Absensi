<?php

namespace App\Exports;

use App\Models\AssessmentCategory;
use App\Exports\Concerns\HasTableStyle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AssessmentCategoryExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents, ShouldAutoSize
{
    use HasTableStyle;

    protected function headerColor(): string { return '7a1a1a'; }
    protected function stripeColor(): string  { return 'FAF0F0'; }

    public function collection()
    {
        return AssessmentCategory::orderBy('name')->get();
    }

    public function headings(): array
    {
        return ['#', 'Nama Kategori', 'Deskripsi', 'Status'];
    }

    private int $rowIndex = 0;

    public function map($row): array
    {
        $this->rowIndex++;
        return [
            $this->rowIndex,
            $row->name,
            $row->description ?? '-',
            $row->is_active ? 'Aktif' : 'Nonaktif',
        ];
    }
}
