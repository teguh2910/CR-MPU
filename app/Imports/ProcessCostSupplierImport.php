<?php

namespace App\Imports;

use App\Models\PartNumber;
use App\Models\ProcessCostSupplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProcessCostSupplierImport implements ToModel, WithHeadingRow, WithValidation
{
    public function prepareForValidation($data, $index)
    {
        return [
            'part_no'            => isset($data['part_no']) ? trim((string) $data['part_no']) : null,
            'process_cost_total' => isset($data['process_cost_total']) && $data['process_cost_total'] !== '' ? $data['process_cost_total'] : null,
        ];
    }

    public function model(array $row)
    {
        $partNumber = PartNumber::where('part_no', $row['part_no'])->first();

        if (! $partNumber) {
            return null;
        }

        return ProcessCostSupplier::updateOrCreate(
            ['part_no_id' => $partNumber->id],
            ['process_cost_total' => $row['process_cost_total']]
        );
    }

    public function rules(): array
    {
        return [
            'part_no'            => 'required|string|max:255',
            'process_cost_total' => 'required|numeric|min:0',
        ];
    }
}
