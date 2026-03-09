<?php

namespace App\Imports;

use App\Models\Activity;
use App\Models\PartNumber;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ActivityImport implements ToModel, WithHeadingRow, WithValidation
{
    public function prepareForValidation($data, $index)
    {
        // Helper function to parse Excel dates
        $parseDate = function ($value) {
            if (empty($value)) {
                return null;
            }
            
            // If it's a numeric value, treat it as Excel serial date
            if (is_numeric($value)) {
                try {
                    return Carbon::createFromFormat('Y-m-d', '1899-12-30')
                        ->addDays((int) $value)
                        ->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            }
            
            // Try to parse as date string
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        };

        // Cast fields to appropriate types before validation
        return [
            'part_no' => isset($data['part_no']) ? trim((string) $data['part_no']) : null,
            'cr_no' => isset($data['cr_no']) && $data['cr_no'] !== '' ? trim((string) $data['cr_no']) : null,
            'activity' => isset($data['activity']) ? trim((string) $data['activity']) : null,
            'year' => isset($data['year']) && $data['year'] !== '' ? $data['year'] : null,
            'cr_satuan' => isset($data['cr_satuan']) && $data['cr_satuan'] !== '' ? trim((string) $data['cr_satuan']) : null,
            'satuan' => isset($data['satuan']) && $data['satuan'] !== '' ? trim((string) $data['satuan']) : null,
            'plan_svp_month' => $parseDate($data['plan_svp_month'] ?? null),
            'act_svp_month' => $parseDate($data['act_svp_month'] ?? null),
        ];
    }

    public function model(array $row)
    {
        // Data is already processed by prepareForValidation
        $partNo = $row['part_no'];
        
        // Find part number by part_no
        $partNumber = PartNumber::where('part_no', $partNo)->first();
        
        if (!$partNumber) {
            throw new \Exception("Part number '{$partNo}' not found");
        }

        return new Activity([
            'part_number_id' => $partNumber->id,
            'cr_no' => $row['cr_no'],
            'activity' => $row['activity'],
            'year' => $row['year'] !== null ? (int) $row['year'] : null,
            'cr_satuan' => $row['cr_satuan'],
            'satuan' => $row['satuan'],
            'plan_svp_month' => $row['plan_svp_month'],
            'act_svp_month' => $row['act_svp_month'],
        ]);
    }

    public function rules(): array
    {
        return [
            'part_no' => 'required|string|max:255',
            'cr_no' => 'nullable|string|max:255',
            'activity' => 'required|string|max:255',
            'year' => 'nullable|integer|min:2000|max:2100',
            'cr_satuan' => 'nullable|string|max:255',
            'satuan' => 'nullable|string|max:255',
            'plan_svp_month' => 'nullable|date',
            'act_svp_month' => 'nullable|date',
        ];
    }
}
