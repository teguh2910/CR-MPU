<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\PartNumber;
use App\Models\Product;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PartNumberImport implements ToModel, WithHeadingRow, WithValidation
{
    public function prepareForValidation($data, $index)
    {
        // Cast all fields to strings before validation
        return [
            'part_no' => isset($data['part_no']) ? trim((string) $data['part_no']) : null,
            'part_sap' => isset($data['part_sap']) && $data['part_sap'] !== '' ? trim((string) $data['part_sap']) : null,
            'part_name' => isset($data['part_name']) ? trim((string) $data['part_name']) : null,
            'supplier' => isset($data['supplier']) ? trim((string) $data['supplier']) : null,
            'product' => isset($data['product']) ? trim((string) $data['product']) : null,
            'category' => isset($data['category']) ? trim((string) $data['category']) : null,
        ];
    }

    public function model(array $row)
    {
        // Data is already cast to strings by prepareForValidation
        $partNo = $row['part_no'];
        $partSap = $row['part_sap'];
        
        // Lookup or create supplier
        $supplier = Supplier::firstOrCreate(['name' => $row['supplier']]);
        
        // Lookup or create product
        $product = Product::firstOrCreate(['name' => $row['product']]);
        
        // Lookup or create category
        $category = Category::firstOrCreate(['name' => $row['category']]);

        return PartNumber::updateOrCreate(
            ['part_no' => $partNo],
            [
                'part_no' => $partNo,
                'part_sap' => $partSap,
                'part_name' => $row['part_name'],
                'supplier_id' => $supplier->id,
                'product_id' => $product->id,
                'category_id' => $category->id,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'part_no' => 'required|string|max:255',
            'part_sap' => 'nullable|string|max:255',
            'part_name' => 'required|string|max:255',
            'supplier' => 'required|string|max:255',
            'product' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ];
    }
}
