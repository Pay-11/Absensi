<?php

namespace App\Exports;

use App\Models\AnggotaKelas;
use App\Exports\Concerns\HasTableStyle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AnggotaKelasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents, ShouldAutoSize
{
    use HasTableStyle;

    protected function headerColor(): string { return '6a1a7a'; }
    protected function stripeColor(): string  { return 'F7F0FA'; }

    protected $kelasId;

    public function __construct($kelasId = null)
    {
        $this->kelasId = $kelasId;
    }

    public function collection()
    {
        $query = AnggotaKelas::with(['murid', 'kelas.tahunAjar']);
        if ($this->kelasId) {
            $query->where('kelas_id', $this->kelasId);
        }
        return $query->get();
    }

    public function headings(): array
    {
        return ['#', 'Nama Siswa', 'Email', 'NISN', 'Kelas', 'Tahun Ajaran'];
    }

    private int $rowIndex = 0;

    public function map($row): array
    {
        $this->rowIndex++;
        return [
            $this->rowIndex,
            $row->murid->name ?? '-',
            $row->murid->email ?? '-',
            $row->murid->nisn ?? '-',
            $row->kelas->nama_kelas ?? '-',
            $row->kelas->tahunAjar->nama ?? '-',
        ];
    }
}
