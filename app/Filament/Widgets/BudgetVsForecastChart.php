<?php

namespace App\Filament\Widgets;

use App\Models\QtyBudget;
use App\Models\QtyForecast;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use Filament\Forms;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class BudgetVsForecastChart extends Widget implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.budget-vs-forecast-chart';

    public ?int $year = null;
    public ?int $category_id = null;
    public ?int $supplier_id = null;
    public ?int $product_id = null;

    public function mount(): void
    {
        $this->year = QtyBudget::max('year') ?? now()->year;
        $this->category_id = null;
        $this->supplier_id = null;
        $this->product_id = null;
        
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Grid::make(4)
                ->schema([
                    Forms\Components\Select::make('year')
                        ->label('Fiscal Year')
                        ->options(fn() => QtyBudget::distinct()
                            ->pluck('year')
                            ->sortDesc()
                            ->mapWithKeys(fn($year) => [$year => 'FY ' . $year])
                            ->toArray())
                        ->default(QtyBudget::max('year') ?? now()->year)
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
        ];
    }

    public function getChartData(): array
    {
        $selectedYear = $this->year ?? QtyBudget::max('year') ?? now()->year;
        $categoryId = $this->category_id;
        $supplierId = $this->supplier_id;
        $productId = $this->product_id;

        // Build budget query
        $budgetQuery = QtyBudget::select('month', DB::raw('SUM(qty) as total'))
            ->where('year', $selectedYear);

        // Build forecast query
        $forecastQuery = QtyForecast::select('month', DB::raw('SUM(qty) as total'))
            ->where('year', $selectedYear);

        // Apply filters through part_number relationship
        if ($categoryId || $supplierId || $productId) {
            $budgetQuery->whereHas('partNumber', function ($query) use ($categoryId, $supplierId, $productId) {
                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                }
                if ($supplierId) {
                    $query->where('supplier_id', $supplierId);
                }
                if ($productId) {
                    $query->where('product_id', $productId);
                }
            });

            $forecastQuery->whereHas('partNumber', function ($query) use ($categoryId, $supplierId, $productId) {
                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                }
                if ($supplierId) {
                    $query->where('supplier_id', $supplierId);
                }
                if ($productId) {
                    $query->where('product_id', $productId);
                }
            });
        }

        // Get monthly budget totals
        $budgetData = $budgetQuery->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Get monthly forecast totals
        $forecastData = $forecastQuery->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

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
}
