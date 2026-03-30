<?php

namespace App\Imports;

use App\Models\PartNumber;
use App\Models\ToolingSupplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ToolingSupplierImport implements ToModel, WithHeadingRow, WithValidation
{
    public function prepareForValidation($data, $index)
    {
        return [
            'part_no'      => isset($data['part_no']) ? trim((string) $data['part_no']) : null,
            'tooling_price' => isset($data['tooling_price']) && $data['tooling_price'] !== '' ? $data['tooling_price'] : null,
            'depre_per_pcs' => isset($data['depre_per_pcs']) && $data['depre_per_pcs'] !== '' ? $data['depre_per_pcs'] : null,
            'status'        => isset($data['status']) ? trim((string) $data['status']) : 'active',
        ];
    }

    public function model(array $row)
    {
        $partNumber = PartNumber::where('part_no', $row['part_no'])->first();

        if (! $partNumber) {
            return null;
        }

        return ToolingSupplier::updateOrCreate(
            ['part_no_id' => $partNumber->id],
            [
                'tooling_price' => $row['tooling_price'],
                'depre_per_pcs' => $row['depre_per_pcs'],
                'status'        => $row['status'],
            ]
        );
    }

    public function rules(): array
    {
        return [
            'part_no'       => 'required|string|max:255',
            'tooling_price' => 'required|numeric|min:0',
            'depre_per_pcs' => 'required|numeric|min:0',
            'status'        => 'nullable|in:active,inactive',
        ];
    }
}
