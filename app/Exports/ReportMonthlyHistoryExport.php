<?php

namespace App\Exports;

use App\Filament\Pages\ReportMonthly;
use App\Models\ReportMonthlyTransaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportMonthlyHistoryExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function __construct(
        protected int $year,
        protected string $remarks,
    ) {
    }

    public function headings(): array
    {
        $headings = [
            'CR NO.',
            'No',
            'CR Activity',
            'Item CR',
            'Category Product',
            'CRP Expense',
        ];

        foreach (ReportMonthly::months() as $month) {
            $abbr = substr($month, 0, 3);
            $headings[] = "{$abbr} OBG";
            $headings[] = "{$abbr} Act/OL";
        }

        $headings[] = 'Total Amount OBG';
        $headings[] = 'Total Amount Act/OL';

        return $headings;
    }

    public function array(): array
    {
        $transactions = ReportMonthlyTransaction::query()
            ->where('year', $this->year)
            ->where('remarks', $this->remarks)
            ->with([
                'partNumber.activities',
                'partNumber.product',
                'partNumber.category',
            ])
            ->get()
            ->groupBy('part_number_id');

        $activityRows = collect();

        foreach ($transactions as $group) {
            $first = $group->first();
            $partNumber = $first?->partNumber;

            if (!$partNumber) {
                continue;
            }

            $txByMonth = $group->keyBy('month');

            foreach ($partNumber->activities as $activity) {
                $activityRows->push([
                    'partNumber' => $partNumber,
                    'activity' => $activity,
                    'txByMonth' => $txByMonth,
                ]);
            }
        }

        $activityRows = $activityRows
            ->sortBy(fn (array $row) => strtolower((string) ($row['activity']->cr_no ?? '')))
            ->values();

        $rows = [];
        $no = 1;

        foreach ($activityRows as $row) {
            $partNumber = $row['partNumber'];
            $activity = $row['activity'];
            $txByMonth = $row['txByMonth'];

            $line = [
                $activity->cr_no ?? '-',
                $no++,
                $activity->activity ?? '-',
                trim(($partNumber->part_no ?? '-') . ' - ' . ($partNumber->part_name ?? '-')),
                $partNumber->product?->name ?? '-',
                $partNumber->category?->name ?? '-',
            ];

            $rowTotalObg = 0;
            $rowTotalActOl = 0;
            $crSatuan = $this->normalizeToNumber($activity->cr_satuan ?? null);

            foreach (ReportMonthly::months() as $month) {
                $qtyBudget = (int) ($txByMonth->get($month)?->qty_budget ?? 0);
                $qtyForecast = (int) ($txByMonth->get($month)?->qty_forecast ?? 0);

                $obg = $qtyBudget * $crSatuan;
                $actOl = $qtyForecast * $crSatuan;

                $rowTotalObg += $obg;
                $rowTotalActOl += $actOl;

                $line[] = $obg;
                $line[] = $actOl;
            }

            $line[] = $rowTotalObg;
            $line[] = $rowTotalActOl;

            $rows[] = $line;
        }

        return $rows;
    }

    protected function normalizeToNumber(mixed $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        $raw = preg_replace('/[^0-9,.-]/', '', (string) $value);

        if ($raw === '' || $raw === null) {
            return 0;
        }

        $hasComma = str_contains($raw, ',');
        $hasDot = str_contains($raw, '.');

        if ($hasComma && $hasDot) {
            if (strrpos($raw, ',') > strrpos($raw, '.')) {
                $raw = str_replace('.', '', $raw);
                $raw = str_replace(',', '.', $raw);
            } else {
                $raw = str_replace(',', '', $raw);
            }
        } elseif ($hasComma) {
            $raw = str_replace(',', '.', $raw);
        }

        return (int) round((float) $raw);
    }
}
