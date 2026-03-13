<?php

namespace App\Filament\Resources\ReportMonthlyTransactionResource\Pages;

use App\Filament\Pages\ReportMonthly;
use App\Filament\Resources\ReportMonthlyTransactionResource;
use App\Models\Activity;
use App\Models\ReportMonthlyTransaction;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

class ShowReportMonthlyTransaction extends Page
{
    protected static string $resource = ReportMonthlyTransactionResource::class;

    protected static string $view = 'filament.resources.report-monthly-transaction-resource.pages.show-report-monthly-transaction';

    public string $remarks = '';

    public int $year = 0;

    public function mount(int | string $year, string $remarksKey): void
    {
        $this->year = (int) $year;
        $this->remarks = $this->decodeRemarksKey($remarksKey);

        abort_if($this->remarks === '', 404);

        $exists = ReportMonthlyTransaction::query()
            ->where('year', $this->year)
            ->where('remarks', $this->remarks)
            ->exists();

        abort_unless($exists, 404);
    }

    public function getTitle(): string
    {
        return 'Monthly Report History';
    }

    public function months(): array
    {
        return ReportMonthly::months();
    }

    #[Computed]
    public function activityRows(): Collection
    {
        $transactions = ReportMonthlyTransaction::query()
            ->where('remarks', $this->remarks)
            ->where('year', $this->year)
            ->with([
                'partNumber.activities',
                'partNumber.product',
                'partNumber.category',
            ])
            ->get()
            ->groupBy('part_number_id');

        $rows = collect();

        foreach ($transactions as $group) {
            $first = $group->first();
            $partNumber = $first?->partNumber;

            if (!$partNumber) {
                continue;
            }

            $txByMonth = $group->keyBy('month');

            foreach ($partNumber->activities as $activity) {
                $rows->push([
                    'partNumber' => $partNumber,
                    'activity' => $activity,
                    'txByMonth' => $txByMonth,
                ]);
            }
        }

        return $rows
            ->sortBy(fn (array $row) => strtolower((string) ($row['activity']->cr_no ?? '')))
            ->values();
    }

    public function resolveActivityOBG(Activity $activity, Collection $txByMonth, string $month): int
    {
        $qty = (int) ($txByMonth->get($month)?->qty_budget ?? 0);

        return $qty * $this->normalizeToNumber($activity->cr_satuan ?? null);
    }

    public function resolveActivityActOL(Activity $activity, Collection $txByMonth, string $month): int
    {
        $qty = (int) ($txByMonth->get($month)?->qty_forecast ?? 0);

        return $qty * $this->normalizeToNumber($activity->cr_satuan ?? null);
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

    protected function decodeRemarksKey(string $remarksKey): string
    {
        $normalized = strtr($remarksKey, '-_', '+/');
        $padding = strlen($normalized) % 4;

        if ($padding > 0) {
            $normalized .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode($normalized, true);

        return is_string($decoded) ? trim($decoded) : '';
    }
}
