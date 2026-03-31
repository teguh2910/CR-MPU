<?php

namespace App\Filament\Pages;

use App\Models\Category;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Exports\ListPricePartNumberExport;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class ListPricePartNumber extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Transaction';

    protected static ?string $navigationLabel = 'List Price Part Number';

    protected static ?int $navigationSort = 11;

    protected static string $view = 'filament.pages.list-price-part-number';

    public ?array $data = [];

    public function mount(): void
    {
        $today = now()->toDateString();

        $this->form->fill([
            'category_id' => null,
            'period_from' => $today,
            'period_to' => $today,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('category_id')
                    ->label('Category')
                    ->options(fn (): array => Category::query()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray())
                    ->searchable()
                    ->preload()
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

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('part_no')
                    ->label('Part No')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('part_name')
                    ->label('Part Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),

                // RM Price
                TextColumn::make('rm_price_idr_total')
                    ->label('RM Price IDR')
                    ->alignEnd()
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float) $state, 0, ',', '.')),

                // Process Cost
                TextColumn::make('process_cost_total')
                    ->label('Process Cost')
                    ->alignEnd()
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float) $state, 0, ',', '.')),

                // FOH & Profiy
                TextColumn::make('foh_profiy_amount')
                    ->label('FOH & Profiy')
                    ->alignEnd()
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(function ($state, $record) {
                        $rmPrice = (float) ($record->getAttributes()['rm_price_idr_total'] ?? 0);
                        $fohPercentage = (float) ($record->getAttributes()['foh_percentage_total'] ?? 0);
                        $fohAmount = ($rmPrice * $fohPercentage) / 100;
                        return 'Rp ' . number_format($fohAmount, 0, ',', '.');
                    }),

                // Other Cost
                TextColumn::make('other_cost_total')
                    ->label('Other Cost')
                    ->alignEnd()
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float) $state, 0, ',', '.')),

                // Tooling Cost
                TextColumn::make('tooling_cost_total')
                    ->label('Tooling Cost')
                    ->alignEnd()
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float) $state, 0, ',', '.')),

                // Grand Total
                TextColumn::make('grand_total')
                    ->label('Grand Total')
                    ->alignEnd()
                    ->weight('bold')
                    ->color('danger')
                    ->formatStateUsing(function ($state, $record) {
                        $attrs = $record->getAttributes();
                        $rmPrice = (float) ($attrs['rm_price_idr_total'] ?? 0);
                        $processCost = (float) ($attrs['process_cost_total'] ?? 0);
                        $fohPercentage = (float) ($attrs['foh_percentage_total'] ?? 0);
                        $otherCost = (float) ($attrs['other_cost_total'] ?? 0);
                        $toolingCost = (float) ($attrs['tooling_cost_total'] ?? 0);
                        
                        $fohAmount = ($rmPrice * $fohPercentage) / 100;
                        $grandTotal = $rmPrice + $processCost + $fohAmount + $otherCost + $toolingCost;
                        
                        return 'Rp ' . number_format($grandTotal, 0, ',', '.');
                    }),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                // Action to view details
                \Filament\Tables\Actions\Action::make('view_details')
                    ->label('View Breakdown')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn ($record) => "Breakdown: {$record->part_no}")
                    ->modalContent(function ($record) {
                        $periodFrom = (string) ($this->data['period_from'] ?? now()->toDateString());
                        $periodTo = (string) ($this->data['period_to'] ?? now()->toDateString());
                        
                        return view('filament.pages.part-number-breakdown', [
                            'data' => $this->calculatePartNumberBreakdown($record, $periodFrom, $periodTo),
                        ]);
                    }),
            ])
            ->bulkActions([])
            ->defaultSort('part_no')
            ->striped();
    }

    private function getTableQuery(): Builder
    {
        $categoryId = $this->data['category_id'] ?? null;
        $periodFrom = (string) ($this->data['period_from'] ?? '');
        $periodTo = (string) ($this->data['period_to'] ?? '');

        return PartNumber::query()
            ->with(['supplier', 'category'])
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->selectRaw('
                part_numbers.*,
                COALESCE((SELECT SUM(rm_basis_price * rm_weight_gram * (
                    SELECT rate FROM ex_rates 
                    WHERE ex_rates.currency = rm_suppliers.rm_currency
                    AND ex_rates.period_from <= ?
                    AND ex_rates.period_to >= ?
                    LIMIT 1
                )) FROM rm_suppliers 
                WHERE rm_suppliers.part_no_id = part_numbers.id
                AND rm_suppliers.period_from <= ?
                AND rm_suppliers.period_to >= ?), 0) as rm_price_idr_total,
                
                COALESCE((SELECT SUM(process_cost_total) FROM process_cost_suppliers 
                WHERE process_cost_suppliers.part_no_id = part_numbers.id), 0) as process_cost_total,
                
                COALESCE((SELECT SUM(percentage) FROM foh_profiy_suppliers 
                WHERE foh_profiy_suppliers.part_no_id = part_numbers.id), 0) as foh_percentage_total,
                
                COALESCE((SELECT SUM(cost) FROM other_cost_suppliers 
                WHERE other_cost_suppliers.part_no_id = part_numbers.id), 0) as other_cost_total,
                
                COALESCE((SELECT SUM(tooling_price) FROM tooling_suppliers 
                WHERE tooling_suppliers.part_no_id = part_numbers.id), 0) as tooling_cost_total
            ', [$periodTo, $periodFrom, $periodTo, $periodFrom]);
    }

    private function calculatePartNumberBreakdown(PartNumber $partNumber, ?string $periodFrom = null, ?string $periodTo = null): array
    {
        $periodFrom = $periodFrom ?? (string) ($this->data['period_from'] ?? now()->toDateString());
        $periodTo = $periodTo ?? (string) ($this->data['period_to'] ?? now()->toDateString());
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
            'part_number' => $partNumber,
            'rm_price_idr_total' => $rmPriceIdrTotal,
            'process_cost_total' => $processCostTotal,
            'foh_percentage_total' => $fohPercentageTotal,
            'foh_profiy_amount' => $fohProfiyAmount,
            'other_cost_total' => $otherCostTotal,
            'tooling_cost_total' => $toolingCostTotal,
            'grand_total' => $grandTotal,
            'rm_details' => $rmDetails,
            'process_costs' => $processCosts,
            'foh_profiy' => $fohProfiy,
            'other_costs' => $otherCosts,
            'tooling_costs' => $toolingCosts,
        ];
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export to Excel')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    $periodFrom = (string) ($this->data['period_from'] ?? now()->toDateString());
                    $periodTo = (string) ($this->data['period_to'] ?? now()->toDateString());
                    $categoryId = (int) ($this->data['category_id'] ?? null);

                    return Excel::download(new ListPricePartNumberExport($periodFrom, $periodTo, $categoryId), 'list_price_part_number_' . now()->format('Ymd_His') . '.xlsx');
                }),
        ];
    }
}
