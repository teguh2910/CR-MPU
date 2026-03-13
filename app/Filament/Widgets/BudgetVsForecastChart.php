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

    // Public properties are required by Livewire validation. We keep them
    // here even though the widget primarily reads filter values from the
    // form state. The properties will be kept in sync so there are no
    // discrepancies. We avoid type hints so Livewire can assign arrays when
    // it occasionally does during hydration (see runtime bug report).
    public $year = null;
    public $category_id = null;
    public $supplier_id = null;
    public $product_id = null;

    public function mount(): void
    {
        // initialize the form with a default year (max year in budgets)
        $this->form->fill([
            'year' => QtyBudget::max('year') ?? now()->year,
            'category_id' => null,
            'supplier_id' => null,
            'product_id' => null,
        ]);

        // also populate the public properties so Livewire doesn't complain
        $this->year = $this->normalizeValue($this->form->getState('year'));
        $this->category_id = $this->normalizeValue($this->form->getState('category_id'));
        $this->supplier_id = $this->normalizeValue($this->form->getState('supplier_id'));
        $this->product_id = $this->normalizeValue($this->form->getState('product_id'));
    }

    /**
     * Normalize values to prevent arrays being passed to select fields.
     * Livewire occasionally assigns arrays during hydration.
     */
    protected function normalizeValue($value)
    {
        if (is_array($value)) {
            return null;
        }
        return $value;
    }

    /**
     * Livewire lifecycle hooks to normalize properties when updated
     */
    public function updatedYear($value)
    {
        $this->year = $this->normalizeValue($value);
    }

    public function updatedCategoryId($value)
    {
        $this->category_id = $this->normalizeValue($value);
    }

    public function updatedSupplierId($value)
    {
        $this->supplier_id = $this->normalizeValue($value);
    }

    public function updatedProductId($value)
    {
        $this->product_id = $this->normalizeValue($value);
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
        // pull values from the form state, falling back to the public
        // properties if they somehow aren't set yet (defensive).
        $state = $this->form->getState();

        $selectedYear = $state['year'] ?? $this->year ?? QtyBudget::max('year') ?? now()->year;
        $categoryId   = $state['category_id'] ?? $this->category_id;
        $supplierId   = $state['supplier_id'] ?? $this->supplier_id;
        $productId    = $state['product_id'] ?? $this->product_id;

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

    /**
     * Whenever a bound property updates we also mirror it into the form
     * state so that the `wire:key` and chart data stay in sync with the
     * latest selection.
     */
    public function updated($property, $value)
    {
        if (in_array($property, ['year', 'category_id', 'supplier_id', 'product_id'], true)) {
            // the value might occasionally be an array when Livewire sends the
            // entire form state - ignore those cases since the form already has
            // correct data.
            if (is_array($value)) {
                return;
            }

            $this->form->fill([$property => $value]);
        }
    }

    /**
     * Safely read a value from the form state; if Livewire accidentally
     * passes an array (hydration quirk) we fall back to the public property
     * or an empty string. Always returns a scalar string for use in blade.
     */
    public function filterValue(string $key): string
    {
        $val = $this->form->getState($key);

        // if we unexpectedly receive an array from Livewire, ignore it.
        if (is_array($val)) {
            $prop = $this->$key ?? '';

            // the bound public property might also be an array during
            // hydration; make sure we return a scalar string.
            if (is_array($prop)) {
                return '';
            }

            return (string) $prop;
        }

        // normal scalar value
        return (string) $val;
    }

    /**
     * Return the current heading text for the widget. We pull from the
     * form state (via filterValue) so that it reflects the selected year
     * and can be used by the view.
     */
    public function getHeading(): string
    {
        $year = $this->filterValue('year') ?: (QtyBudget::max('year') ?? now()->year);

        return "Budget vs Forecast Amount (FY {$year})";
    }
}


