<?php

namespace App\Imports;

use App\Models\FohProfiySupplier;
use App\Models\PartNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class FohProfiySupplierImport implements ToModel, WithHeadingRow, WithValidation
{
    public function prepareForValidation($data, $index)
    {
        return [
            'part_no'    => isset($data['part_no']) ? trim((string) $data['part_no']) : null,
            'percentage' => isset($data['percentage']) && $data['percentage'] !== '' ? $data['percentage'] : null,
            'remarks'    => isset($data['remarks']) ? trim((string) $data['remarks']) : null,
        ];
    }

    public function model(array $row)
    {
        $partNumber = PartNumber::where('part_no', $row['part_no'])->first();

        if (! $partNumber) {
            return null;
        }

        return FohProfiySupplier::updateOrCreate(
            [
                'part_no_id' => $partNumber->id,
                'remarks'    => $row['remarks'],
            ],
            ['percentage' => $row['percentage']]
        );
    }

    public function rules(): array
    {
        return [
            'part_no'    => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0',
            'remarks'    => 'nullable|string|max:255',
        ];
    }
}
