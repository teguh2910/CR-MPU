<?php

namespace App\Exports;

use App\Models\ExRate;
use App\Models\FohProfiySupplier;
use App\Models\OtherCostSupplier;
use App\Models\PartNumber;
use App\Models\ProcessCostSupplier;
use App\Models\RmSupplier;
use App\Models\ToolingSupplier;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ListPricePartNumberExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    protected string $periodFrom;
    protected string $periodTo;
    protected ?int $categoryId;

    public function __construct(string $periodFrom, string $periodTo, ?int $categoryId)
    {
        $this->periodFrom = $periodFrom;
        $this->periodTo = $periodTo;
        $this->categoryId = $categoryId;
    }

    public function collection(): Collection
    {
        $query = PartNumber::query()
            ->with(['supplier', 'category']);

        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }

        $partNumbers = $query->orderBy('part_no')->get();

        return $partNumbers->map(function (PartNumber $partNumber) {
            return $this->calculatePartNumberBreakdown($partNumber, $this->periodFrom, $this->periodTo);
        });
    }

    private function calculatePartNumberBreakdown(PartNumber $partNumber, string $periodFrom, string $periodTo): array
    {
        $partNoId = $partNumber->id;

        // Get RM Suppliers
        $rmSuppliers = RmSupplier::query()
            ->where('part_no_id', $partNoId)
            ->whereDate('period_from', '<=', $periodTo)
            ->whereDate('period_to', '>=', $periodFrom)
            ->orderBy('period_from')
            ->get();

        $currencies = $rmSuppliers
            ->pluck('rm_currency')
            ->filter()
            ->unique()
            ->values();

        $exRatesGrouped = ExRate::query()
            ->whereIn('currency', $currencies)
            ->whereDate('period_from', '<=', $periodTo)
            ->whereDate('period_to', '>=', $periodFrom)
            ->orderByDesc('period_from')
            ->get()
            ->groupBy('currency');

        $rmPriceIdrTotal = 0;
        $rmDetails = [];

        foreach ($rmSuppliers as $rm) {
            $matchedRate = $exRatesGrouped
                ->get((string) $rm->rm_currency, collect())
                ->first(function (ExRate $rate) use ($periodFrom, $periodTo) {
                    return (string) $rate->period_from <= $periodTo
                        && (string) $rate->period_to >= $periodFrom;
                });

            $rmBasisPrice = (float) $rm->rm_basis_price;
            $rmWeightGram = (float) ($rm->rm_weight_gram ?? 0);
            $exRate = $matchedRate ? (float) $matchedRate->rate : null;
            $rmPriceIdr = $exRate !== null
                ? ($rmBasisPrice * $exRate * $rmWeightGram)
                : 0;

            $rmPriceIdrTotal += $rmPriceIdr;

            $rmDetails[] = [
                'currency' => $rm->rm_currency,
                'basis_price' => $rmBasisPrice,
                'weight' => $rmWeightGram,
                'ex_rate' => $exRate,
                'price_idr' => $rmPriceIdr,
            ];
        }

        // Get Process Costs
        $processCosts = ProcessCostSupplier::query()
            ->where('part_no_id', $partNoId)
            ->orderByDesc('id')
            ->get();
        $processCostTotal = (float) $processCosts->sum('process_cost_total');

        // Get FOH & Profiy
        $fohProfiy = FohProfiySupplier::query()
            ->where('part_no_id', $partNoId)
            ->orderByDesc('id')
            ->get();
        $fohPercentageTotal = (float) $fohProfiy->sum('percentage');
        $fohProfiyAmount = ($rmPriceIdrTotal * $fohPercentageTotal) / 100;

        // Get Other Costs
        $otherCosts = OtherCostSupplier::query()
            ->where('part_no_id', $partNoId)
            ->orderByDesc('id')
            ->get();
        $otherCostTotal = (float) $otherCosts->sum('cost');

        // Get Tooling Costs
        $toolingCosts = ToolingSupplier::query()
            ->where('part_no_id', $partNoId)
            ->orderByDesc('id')
            ->get();
        $toolingCostTotal = (float) $toolingCosts->sum('tooling_price');

        // Calculate Grand Total
        $grandTotal = $rmPriceIdrTotal + $processCostTotal + $fohProfiyAmount + $otherCostTotal + $toolingCostTotal;

        return [
            'part_no' => $partNumber->part_no,
            'part_name' => $partNumber->part_name,
            'supplier_name' => $partNumber->supplier?->name ?? '-',
            'category_name' => $partNumber->category?->name ?? '-',
            'rm_currency' => $rmDetails[0]['currency'] ?? '-',
            'rm_basis_price' => $rmDetails[0]['basis_price'] ?? 0,
            'rm_weight_gram' => $rmDetails[0]['weight'] ?? 0,
            'rm_ex_rate' => $rmDetails[0]['ex_rate'] ?? 0,
            'rm_price_idr' => $rmDetails[0]['price_idr'] ?? 0,
            'process_cost_total' => $processCostTotal,
            'foh_percentage_total' => $fohPercentageTotal,
            'foh_profiy_amount' => $fohProfiyAmount,
            'other_cost_total' => $otherCostTotal,
            'tooling_cost_total' => $toolingCostTotal,
            'grand_total' => $grandTotal,
        ];
    }

    public function headings(): array
    {
        return [
            'Part No',
            'Part Name',
            'Supplier',
            'Category',
            'RM Currency',
            'RM Basis Price',
            'RM Weight (g)',
            'RM Ex-Rate',
            'RM Price IDR',
            'Process Cost Total',
            'FOH & Profiy Percentage',
            'FOH & Profiy Amount',
            'Other Cost Total',
            'Tooling Cost Total',
            'Grand Total',
        ];
    }

    public function map($data): array
    {
        return [
            $data['part_no'],
            $data['part_name'],
            $data['supplier_name'],
            $data['category_name'],
            $data['rm_currency'],
            number_format($data['rm_basis_price'], 2),
            number_format($data['rm_weight_gram'], 0),
            number_format($data['rm_ex_rate'], 2),
            number_format($data['rm_price_idr'], 0),
            number_format($data['process_cost_total'], 0),
            number_format($data['foh_percentage_total'], 2) . '% ',
            number_format($data['foh_profiy_amount'], 0),
            number_format($data['other_cost_total'], 0),
            number_format($data['tooling_cost_total'], 0),
            number_format($data['grand_total'], 0),
        ];
    }
}
