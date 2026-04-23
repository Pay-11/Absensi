<?php

namespace App\Exports;

use App\Exports\Concerns\HasTableStyle;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class RekapAbsensiExport implements FromArray, WithHeadings, WithStyles, WithEvents, ShouldAutoSize, WithTitle
{
    use HasTableStyle;

    protected function headerColor(): string { return '155e75'; }
    protected function stripeColor(): string  { return 'ecfeff'; }

    protected array $headingRow;
    protected array $rows;
    protected string $sheetTitle;

    public function __construct(array $headingRow, array $rows, string $sheetTitle = 'Rekap Absensi')
    {
        $this->headingRow = $headingRow;
        $this->rows       = $rows;
        $this->sheetTitle = $sheetTitle;
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function headings(): array
    {
        return $this->headingRow;
    }

    public function array(): array
    {
        return $this->rows;
    }
}
