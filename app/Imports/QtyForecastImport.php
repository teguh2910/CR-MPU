<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\Models\PartNumber;
use App\Models\QtyForecast;
use App\Models\UpdateQtyMonth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QtyForecastImport implements ToCollection, WithHeadingRow
{
    private const MONTH_ALIASES = [
        'January' => ['january', 'jan'],
        'February' => ['february', 'feb'],
        'March' => ['march', 'mar'],
        'April' => ['april', 'apr'],
        'May' => ['may'],
        'June' => ['june', 'jun'],
        'July' => ['july', 'jul'],
        'August' => ['august', 'aug'],
        'September' => ['september', 'sep', 'sept'],
        'October' => ['october', 'oct'],
        'November' => ['november', 'nov'],
        'December' => ['december', 'dec'],
    ];

    /**
     * Supports 2 formats:
     * 1) Row format: update_qty_month_id, part_no, month, qty, year
     * 2) Pivot format: update_qty_month_id, part_no, year, jan, feb, mar, ... dec
     */
    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $partNo = trim((string) ($row['part_no'] ?? ''));

            if ($partNo === '') {
                continue;
            }

            $updateQtyMonthId = $this->parseUpdateQtyMonthId($row['update_qty_month_id'] ?? null, $index);
            $partNumber = PartNumber::where('part_no', $partNo)->first();

            if (!$partNumber) {
                throw new \Exception("Part number '{$partNo}' not found at row " . ($index + 2));
            }

            $hasRowFormat = isset($row['month']) || isset($row['qty']);

            if ($hasRowFormat) {
                $this->importRowFormat($row, $updateQtyMonthId, $partNumber->id, $index);
                continue;
            }

            $this->importPivotFormat($row, $updateQtyMonthId, $partNumber->id, $index);
        }
    }

    private function importRowFormat($row, int $updateQtyMonthId, int $partNumberId, int $index): void
    {
        $rawMonth = trim((string) ($row['month'] ?? ''));
        $qty = $row['qty'] ?? null;
        $year = $this->parseYear($row['year'] ?? null, $index);

        if ($rawMonth === '' || $qty === null || $qty === '') {
            throw new \Exception('Row format requires month and qty at row ' . ($index + 2));
        }

        if (!is_numeric($qty)) {
            throw new \Exception('Qty must be numeric at row ' . ($index + 2));
        }

        $month = $this->normalizeMonth($rawMonth, $index);

        QtyForecast::updateOrCreate(
            [
                'update_qty_month_id' => $updateQtyMonthId,
                'part_number_id' => $partNumberId,
                'month' => $month,
                'year' => $year,
            ],
            [
                'qty' => (int) $qty,
            ]
        );
    }

    private function importPivotFormat($row, int $updateQtyMonthId, int $partNumberId, int $index): void
    {
        $year = $this->parseYear($row['year'] ?? null, $index);

        foreach (self::MONTH_ALIASES as $monthName => $aliases) {
            $qty = null;

            foreach ($aliases as $alias) {
                if (isset($row[$alias]) && $row[$alias] !== '' && $row[$alias] !== null) {
                    $qty = $row[$alias];
                    break;
                }
            }

            if ($qty === null || $qty === '') {
                continue;
            }

            if (!is_numeric($qty)) {
                throw new \Exception("Qty for {$monthName} must be numeric at row " . ($index + 2));
            }

            QtyForecast::updateOrCreate(
                [
                    'update_qty_month_id' => $updateQtyMonthId,
                    'part_number_id' => $partNumberId,
                    'month' => $monthName,
                    'year' => $year,
                ],
                [
                    'qty' => (int) $qty,
                ]
            );
        }
    }

    private function parseUpdateQtyMonthId(mixed $rawId, int $index): int
    {
        if ($rawId === null || $rawId === '') {
            throw new \Exception('update_qty_month_id is required at row ' . ($index + 2));
        }

        if (!is_numeric($rawId)) {
            throw new \Exception('update_qty_month_id must be numeric at row ' . ($index + 2));
        }

        $id = (int) $rawId;

        if (!UpdateQtyMonth::query()->whereKey($id)->exists()) {
            throw new \Exception("update_qty_month_id '{$id}' not found at row " . ($index + 2));
        }

        return $id;
    }

    private function parseYear(mixed $rawYear, int $index): int
    {
        if ($rawYear === null || $rawYear === '') {
            throw new \Exception('Year is required at row ' . ($index + 2));
        }

        if (!is_numeric($rawYear)) {
            throw new \Exception('Year must be numeric at row ' . ($index + 2));
        }

        $year = (int) $rawYear;

        if ($year < 2000 || $year > 2100) {
            throw new \Exception('Year must be between 2000 and 2100 at row ' . ($index + 2));
        }

        return $year;
    }

    private function normalizeMonth(string $rawMonth, int $index): string
    {
        $value = strtolower(trim($rawMonth));

        foreach (self::MONTH_ALIASES as $monthName => $aliases) {
            if (in_array($value, $aliases, true)) {
                return $monthName;
            }
        }

        throw new \Exception("Invalid month '{$rawMonth}' at row " . ($index + 2));
    }
}
