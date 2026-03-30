<?php

namespace App\Exports;

use App\Models\Rfq;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RfqExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function __construct(protected Rfq $rfq) {}

    public function headings(): array
    {
        return ['Section', 'No', 'Column 1', 'Column 2', 'Column 3'];
    }

    public function array(): array
    {
        $rows = [
            ['Header', '', 'RFQ ID', (string) $this->rfq->id, ''],
            ['Header', '', 'RFQ Date', optional($this->rfq->rfq_date)->format('Y-m-d') ?? '-', ''],
            ['Header', '', 'To Company', (string) $this->rfq->to_company, ''],
            ['Header', '', 'To PIC', (string) $this->rfq->to_pic, ''],
            ['Header', '', 'Product Name', (string) $this->rfq->product_name, ''],
            ['Header', '', 'Due Date', optional($this->rfq->quotation_due_date)->format('Y-m-d') ?? '-', ''],
            ['', '', '', '', ''],
        ];

        foreach ($this->rfq->items as $index => $item) {
            $rows[] = [
                'Part Items',
                (string) ($index + 1),
                (string) $item->part_number,
                (string) $item->part_name,
                (string) $item->qty_mon,
            ];
        }

        $rows[] = ['', '', '', '', ''];

        foreach ($this->rfq->exchangeRates as $index => $rate) {
            $rows[] = [
                'Exchange Rates',
                (string) ($index + 1),
                strtoupper((string) $rate->currency),
                (string) $rate->rate,
                'IDR',
            ];
        }

        return $rows;
    }
}
