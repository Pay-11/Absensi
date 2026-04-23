<?php

namespace App\Exports\Concerns;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

trait HasTableStyle
{
    // Override method ini di child class untuk ganti warna
    protected function headerColor(): string { return '1a7431'; }
    protected function stripeColor(): string  { return 'F2F8F4'; }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $this->headerColor()]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastCol = $sheet->getHighestColumn();
                $range   = "A1:{$lastCol}{$lastRow}";

                // Border tipis semua sel
                $sheet->getStyle($range)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);

                // Border tebal luar
                $sheet->getStyle($range)->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color'       => ['rgb' => '888888'],
                        ],
                    ],
                ]);

                // Tinggi header
                $sheet->getRowDimension(1)->setRowHeight(26);

                // Zebra row
                for ($row = 2; $row <= $lastRow; $row++) {
                    $color = ($row % 2 === 0) ? $this->stripeColor() : 'FFFFFF';
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")
                        ->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($color);

                    $sheet->getRowDimension($row)->setRowHeight(20);
                }

                // Center kolom nomor (#)
                if ($lastRow > 1) {
                    $sheet->getStyle("A2:A{$lastRow}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            },
        ];
    }
}
