<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QtyForecastPivotTemplateExport implements FromArray, WithHeadings
{
    /**
     * @return array
     */
    public function array(): array
    {
        return [
            [
                1, // update_qty_month_id (example)
                'PART-001', // part_no (example)
                2026, // year
                100, // jan
                120, // feb
                130, // mar
                140, // apr
                150, // may
                160, // jun
                170, // jul
                180, // aug
                190, // sep
                200, // oct
                210, // nov
                220, // dec
            ],
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
            'year',
            'jan',
            'feb',
            'mar',
            'apr',
            'may',
            'jun',
            'jul',
            'aug',
            'sep',
            'oct',
            'nov',
            'dec',
        ];
    }
}
