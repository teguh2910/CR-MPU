<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MissingPartNumbersExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function __construct(protected array $missingPartNos) {}

    public function array(): array
    {
        return collect($this->missingPartNos)
            ->map(fn ($partNo) => [$partNo])
            ->toArray();
    }

    public function headings(): array
    {
        return ['part_no (not found — please create these first)'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFCC0000']]],
        ];
    }
}
