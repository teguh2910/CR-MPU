<?php

namespace App\Imports;

use App\Models\OtherCostSupplier;
use App\Models\PartNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class OtherCostSupplierImport implements ToModel, WithHeadingRow, WithValidation
{
    public function prepareForValidation($data, $index)
    {
        return [
            'part_no' => isset($data['part_no']) ? trim((string) $data['part_no']) : null,
            'remark'  => isset($data['remark']) ? trim((string) $data['remark']) : null,
            'cost'    => isset($data['cost']) && $data['cost'] !== '' ? $data['cost'] : null,
        ];
    }

    public function model(array $row)
    {
        $partNumber = PartNumber::where('part_no', $row['part_no'])->first();

        if (! $partNumber) {
            return null;
        }

        return OtherCostSupplier::updateOrCreate(
            [
                'part_no_id' => $partNumber->id,
                'remark'     => $row['remark'],
            ],
            ['cost' => $row['cost']]
        );
    }

    public function rules(): array
    {
        return [
            'part_no' => 'required|string|max:255',
            'remark'  => 'required|string|max:255',
            'cost'    => 'required|numeric|min:0',
        ];
    }
}
