<?php

namespace App\Imports;

use App\Models\ExRate;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ExRateImport implements ToModel, WithHeadingRow, WithValidation
{
    public function prepareForValidation($data, $index)
    {
        return [
            'currency'    => isset($data['currency']) ? trim((string) $data['currency']) : null,
            'period_from' => isset($data['period_from']) && $data['period_from'] !== '' ? trim((string) $data['period_from']) : null,
            'period_to'   => isset($data['period_to']) && $data['period_to'] !== '' ? trim((string) $data['period_to']) : null,
            'rate'        => isset($data['rate']) && $data['rate'] !== '' ? $data['rate'] : null,
        ];
    }

    public function model(array $row)
    {
        return ExRate::updateOrCreate(
            [
                'currency'    => $row['currency'],
                'period_from' => $row['period_from'],
                'period_to'   => $row['period_to'],
            ],
            [
                'rate' => $row['rate'],
            ]
        );
    }

    public function rules(): array
    {
        return [
            'currency'    => 'required|string|max:10',
            'period_from' => 'required|date',
            'period_to'   => 'required|date|after_or_equal:period_from',
            'rate'        => 'required|numeric|min:0',
        ];
    }
}
