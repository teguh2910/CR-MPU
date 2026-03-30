<?php

namespace App\Filament\Pages;

use App\Models\ExRate;
use App\Models\FohProfiySupplier;
use App\Models\OtherCostSupplier;
use App\Models\PartNumber;
use App\Models\ProcessCostSupplier;
use App\Models\RmSupplier;
use App\Models\ToolingSupplier;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

class CheckQuotation extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected static ?string $navigationGroup = 'Transaction';

    protected static ?string $navigationLabel = 'Check Quotation';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.check-quotation';

    public ?array $data = [];

    public function mount(): void
    {
        $today = now()->toDateString();

        $this->form->fill([
            'part_no_id' => null,
            'period_from' => $today,
            'period_to' => $today,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('part_no_id')
                    ->label('Part No')
                    ->options(fn (): array => PartNumber::query()
                        ->orderBy('part_no')
                        ->get()
                        ->mapWithKeys(fn (PartNumber $part) => [
                            $part->id => $part->part_no . ' - ' . $part->part_name,
                        ])
                        ->toArray())
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live(),

                DatePicker::make('period_from')
                    ->label('Period From')
                    ->native(false)
                    ->displayFormat('d M Y')
                    ->required()
                    ->live(),

                DatePicker::make('period_to')
                    ->label('Period To')
                    ->native(false)
                    ->displayFormat('d M Y')
                    ->afterOrEqual('period_from')
                    ->required()
                    ->live(),
            ])
            ->statePath('data')
            ->columns(3);
    }

    #[Computed]
    public function quotationResult(): ?array
    {
        $partNoId = (int) ($this->data['part_no_id'] ?? 0);
        $periodFrom = (string) ($this->data['period_from'] ?? '');
        $periodTo = (string) ($this->data['period_to'] ?? '');

        if ($partNoId <= 0 || $periodFrom === '' || $periodTo === '') {
            return null;
        }

        $partNumber = PartNumber::query()
            ->with(['supplier', 'product', 'category'])
            ->find($partNoId);

        if (!$partNumber) {
            return null;
        }

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

        $rmRows = $rmSuppliers->map(function (RmSupplier $rm) use ($exRatesGrouped, $periodFrom, $periodTo) {
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

            return [
                'period_from' => $rm->period_from,
                'period_to' => $rm->period_to,
                'rm_currency' => $rm->rm_currency,
                'rm_basis_price' => $rmBasisPrice,
                'rm_weight_gram' => $rmWeightGram,
                'ex_rate' => $exRate,
                'rm_price_idr' => $rmPriceIdr,
            ];
        })->values();

        $processCosts = ProcessCostSupplier::query()
            ->where('part_no_id', $partNoId)
            ->orderByDesc('id')
            ->get();

        $fohProfiy = FohProfiySupplier::query()
            ->where('part_no_id', $partNoId)
            ->orderByDesc('id')
            ->get();

        $otherCosts = OtherCostSupplier::query()
            ->where('part_no_id', $partNoId)
            ->orderByDesc('id')
            ->get();

        $toolingCosts = ToolingSupplier::query()
            ->where('part_no_id', $partNoId)
            ->orderByDesc('id')
            ->get();

        $rmPriceIdrTotal = (float) $rmRows->sum('rm_price_idr');
        $processCostTotal = (float) $processCosts->sum('process_cost_total');
        $fohPercentageTotal = (float) $fohProfiy->sum('percentage');
        $otherCostTotal = (float) $otherCosts->sum('cost');
        $toolingCostTotal = (float) $toolingCosts->sum('tooling_price');

        $fohProfiyAmount = ($rmPriceIdrTotal * $fohPercentageTotal) / 100;
        $grandTotal = $rmPriceIdrTotal + $processCostTotal + $fohProfiyAmount + $otherCostTotal + $toolingCostTotal;

        return [
            'part_number' => $partNumber,
            'period_from' => $periodFrom,
            'period_to' => $periodTo,
            'rm_rows' => $rmRows,
            'process_costs' => $processCosts,
            'foh_profiy' => $fohProfiy,
            'other_costs' => $otherCosts,
            'tooling_costs' => $toolingCosts,
            'rm_price_idr_total' => $rmPriceIdrTotal,
            'process_cost_total' => $processCostTotal,
            'foh_percentage_total' => $fohPercentageTotal,
            'foh_profiy_amount' => $fohProfiyAmount,
            'other_cost_total' => $otherCostTotal,
            'tooling_cost_total' => $toolingCostTotal,
            'grand_total' => $grandTotal,
        ];
    }
}
