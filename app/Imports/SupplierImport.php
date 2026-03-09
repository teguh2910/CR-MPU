<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SupplierImport implements ToModel, WithHeadingRow, WithValidation
{
    public function prepareForValidation($data, $index)
    {
        return [
            'name' => isset($data['name']) ? trim((string) $data['name']) : null,
        ];
    }

    public function model(array $row)
    {
        return Supplier::updateOrCreate(
            ['name' => $row['name']],
            ['name' => $row['name']]
        );
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
}
