<?php

namespace App\Filament\Widgets;

use App\Models\ReportMonthlyTransaction;
use App\Models\QtyBudget;
use App\Models\QtyForecast;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class BudgetVsForecastChart extends Widget implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.budget-vs-forecast-chart';

    public ?array $data = [];

    public function mount(): void
    {
        $latestYear = QtyBudget::max('year') ?? now()->year;
        $latestSnapshot = ReportMonthlyTransaction::query()
            ->where('year', $latestYear)
            ->whereNotNull('remarks')
            ->where('remarks', '!=', '')
            ->orderByDesc('created_at')
            ->first();

        $this->form->fill([
            'year' => $latestYear,
            'remarks' => $latestSnapshot?->remarks,
            'category_id' => null,
            'supplier_id' => null,
            'product_id' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(5)
                    ->schema([
                        Forms\Components\Select::make('year')
                            ->label('Fiscal Year')
                            ->options(fn() => QtyBudget::distinct()
                                ->pluck('year')
                                ->sortDesc()
                                ->mapWithKeys(fn($year) => [$year => 'FY ' . $year])
                                ->toArray())
                            ->live(),
                        
                        Forms\Components\Select::make('remarks')
                            ->label('Snapshot')
                            ->options(function (Forms\Get $get) {
                                $year = $get('year');
                                if (!$year) return [];
                                
                                return ReportMonthlyTransaction::query()
                                    ->where('year', $year)
                                    ->whereNotNull('remarks')
                                    ->where('remarks', '!=', '')
                                    ->distinct()
                                    ->pluck('remarks', 'remarks')
                                    ->toArray();
                            })
                            ->required()
                            ->native(false)
                            ->live(),

                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->options(fn() => Category::orderBy('name')->pluck('name', 'id'))
                            ->placeholder('All Categories')
                            ->live(),
                        
                        Forms\Components\Select::make('supplier_id')
                            ->label('Supplier')
                            ->options(fn() => Supplier::orderBy('name')->pluck('name', 'id'))
                            ->placeholder('All Suppliers')
                            ->searchable()
                            ->live(),
                        
                        Forms\Components\Select::make('product_id')
                            ->label('Product')
                            ->options(fn() => Product::orderBy('name')->pluck('name', 'id'))
                            ->placeholder('All Products')
                            ->live(),
                    ]),
            ])
            ->statePath('data');
    }

    public function getChartData(): array
    {
        $selectedYear = $this->data['year'] ?? QtyBudget::max('year') ?? now()->year;
        $remarks      = $this->data['remarks'] ?? null;
        $categoryId   = $this->data['category_id'] ?? null;
        $supplierId   = $this->data['supplier_id'] ?? null;
        $productId    = $this->data['product_id'] ?? null;

        $budgetData = [];
        $forecastData = [];

        if ($remarks) {
            // Data from ReportMonthlyTransaction (Snapshot)
            $query = ReportMonthlyTransaction::query()
                ->where('year', $selectedYear)
                ->where('remarks', $remarks);

            if ($categoryId || $supplierId || $productId) {
                $query->whereHas('partNumber', function ($q) use ($categoryId, $supplierId, $productId) {
                    if ($categoryId) {
                        $q->where('category_id', $categoryId);
                    }
                    if ($supplierId) {
                        $q->where('supplier_id', $supplierId);
                    }
                    if ($productId) {
                        $q->where('product_id', $productId);
                    }
                });
            }

            $results = $query->select('month', 
                    DB::raw('SUM(qty_budget) as total_budget'),
                    DB::raw('SUM(qty_forecast) as total_forecast'))
                ->groupBy('month')
                ->get();

            $budgetData = $results->pluck('total_budget', 'month')->toArray();
            $forecastData = $results->pluck('total_forecast', 'month')->toArray();
        }

        // Month names mapping - Fiscal Year (Apr-Mar)
        $monthMapping = [
            'April' => 'Apr',
            'May' => 'May',
            'June' => 'Jun',
            'July' => 'Jul',
            'August' => 'Aug',
            'September' => 'Sep',
            'October' => 'Oct',
            'November' => 'Nov',
            'December' => 'Dec',
            'January' => 'Jan',
            'February' => 'Feb',
            'March' => 'Mar',
        ];

        $budgetValues = [];
        $forecastValues = [];
        $labels = [];

        foreach ($monthMapping as $fullMonth => $shortMonth) {
            $budgetValues[] = $budgetData[$fullMonth] ?? 0;
            $forecastValues[] = $forecastData[$fullMonth] ?? 0;
            $labels[] = $shortMonth;
        }

        return [
            'type' => 'bar',
            'data' => [
                'datasets' => [
                    [
                        'label' => 'Budget Amount',
                        'data' => $budgetValues,
                        'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                        'borderColor' => 'rgb(59, 130, 246)',
                        'borderWidth' => 2,
                    ],
                    [
                        'label' => 'Forecast Amount',
                        'data' => $forecastValues,
                        'backgroundColor' => 'rgba(251, 191, 36, 0.5)',
                        'borderColor' => 'rgb(251, 191, 36)',
                        'borderWidth' => 2,
                    ],
                ],
                'labels' => $labels,
            ],
            'options' => [
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * Safely read a value from the form data. Returns a scalar string for use in blade.
     */
    public function filterValue(string $key): string
    {
        $val = $this->data[$key] ?? '';

        if (is_array($val)) {
            return '';
        }

        return (string) $val;
    }

    /**
     * Return the current heading text for the widget.
     */
    public function getHeading(): string
    {
        $year = $this->filterValue('year') ?: (QtyBudget::max('year') ?? now()->year);
        $remarks = $this->filterValue('remarks');

        $title = "Budget vs Forecast Amount (FY {$year})";
        if ($remarks) {
            $title .= " - Snapshot: {$remarks}";
        } else {
            $title .= " - No Snapshot Selected";
        }

        return $title;
    }
}


