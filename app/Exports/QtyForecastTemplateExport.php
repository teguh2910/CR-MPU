<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QtyForecastTemplateExport implements FromArray, WithHeadings
{
    /**
     * @return array
     */
    public function array(): array
    {
        // Return empty array for template or sample data
        return [
            [
                1, // update_qty_month_id (example)
                'PART-001', // part_no (example)
                'January', // month
                100, // qty
                2026, // year
            ]
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'update_qty_month_id',
            'part_no',
            'month',
            'qty',
            'year',
        ];
    }
}
