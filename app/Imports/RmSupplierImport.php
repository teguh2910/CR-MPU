<?php

namespace App\Imports;

use App\Models\PartNumber;
use App\Models\RmSupplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class RmSupplierImport implements ToModel, WithHeadingRow, WithValidation
{
    public function prepareForValidation($data, $index)
    {
        return [
            'part_no'        => isset($data['part_no']) ? trim((string) $data['part_no']) : null,
            'period_from'    => isset($data['period_from']) && $data['period_from'] !== '' ? trim((string) $data['period_from']) : null,
            'period_to'      => isset($data['period_to']) && $data['period_to'] !== '' ? trim((string) $data['period_to']) : null,
            'rm_currency'    => isset($data['rm_currency']) ? trim((string) $data['rm_currency']) : null,
            'rm_basis_price' => isset($data['rm_basis_price']) && $data['rm_basis_price'] !== '' ? $data['rm_basis_price'] : null,
            'rm_weight_gram' => isset($data['rm_weight_gram']) && $data['rm_weight_gram'] !== '' ? $data['rm_weight_gram'] : null,
        ];
    }

    public function model(array $row)
    {
        $partNumber = PartNumber::where('part_no', $row['part_no'])->first();

        if (! $partNumber) {
            return null;
        }

        return RmSupplier::updateOrCreate(
            [
                'part_no_id'  => $partNumber->id,
                'period_from' => $row['period_from'],
                'period_to'   => $row['period_to'],
                'rm_currency' => $row['rm_currency'],
            ],
            [
                'rm_basis_price' => $row['rm_basis_price'],
                'rm_weight_gram' => $row['rm_weight_gram'],
            ]
        );
    }

    public function rules(): array
    {
        return [
            'part_no'        => 'required|string|max:255',
            'period_from'    => 'required|date',
            'period_to'      => 'required|date|after_or_equal:period_from',
            'rm_currency'    => 'required|string|max:10',
            'rm_basis_price' => 'required|numeric|min:0',
            'rm_weight_gram' => 'required|numeric|min:0',
        ];
    }
}
