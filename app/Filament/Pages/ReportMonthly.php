<?php

namespace App\Filament\Pages;

use App\Models\Activity;
use App\Models\PartNumber;
use App\Models\ReportMonthlyTransaction;
use App\Models\UpdateQtyMonth;
use Filament\Forms\Get;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

class ReportMonthly extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationGroup = 'Report';

    protected static ?string $navigationLabel = 'Monthly Report';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.report-monthly';

    public ?array $data = [];

    public function mount(): void
    {
        $defaultYear = (int) (UpdateQtyMonth::query()->max('year') ?: date('Y'));

        $this->form->fill([
            'year' => $defaultYear,
            'remarks' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('year')
                    ->label('Year')
                    ->options(
                        collect(range(2020, 2035))
                            ->mapWithKeys(fn (int $year) => [$year => (string) $year])
                            ->toArray()
                    )
                    ->required()
                    ->live(),

                TextInput::make('remarks')
                    ->label('Remarks')
                    ->placeholder("Example: Act Apr'25+OL")
                    ->required()
                    ->maxLength(100),

                Grid::make(3)
                    ->schema(
                        collect(self::months())
                            ->map(function (string $month): Select {
                                $key = strtolower($month);

                                return Select::make("update_qty_month_id_{$key}")
                                    ->label("{$month} Act/OL Update")
                                    ->options(function (Get $get): array {
                                        $year = (int) ($get('year') ?? 0);

                                        if ($year <= 0) {
                                            return [];
                                        }

                                                $forecasts = UpdateQtyMonth::query()
                                            ->where('year', $year)
                                            ->orderByDesc('id')
                                            ->get()
                                            ->mapWithKeys(fn (UpdateQtyMonth $record) => [
                                                $record->id => "{$record->month} {$record->year} #{$record->id}",
                                            ])
                                            ->toArray();

                                        return ['budget' => 'Qty Budget'] + $forecasts;
                                    })
                                    ->nullable()
                                    ->native(false)
                                    ->searchable()
                                    ->live();
                            })
                            ->all()
                    ),
            ])
            ->statePath('data')
            ->columns(1);
    }

    #[Computed]
    public function reportRows(): Collection
    {
        $year = isset($this->data['year']) ? (int) $this->data['year'] : null;

        if (!$year) {
            return collect();
        }

        return PartNumber::query()
            ->whereHas('activities')
            ->with([
                'activities',
                'product',
                'category',
                'qtyBudgets' => fn ($q) => $q->where('year', $year),
                'qtyForecasts' => fn ($q) => $q->where('year', $year),
            ])
            ->orderBy('part_no')
            ->get();
    }

    public function saveReport(): void
    {
        $state = $this->form->getState();

        $year = (int) $state['year'];
        $remarks = trim((string) ($state['remarks'] ?? ''));

        if ($remarks === '') {
            Notification::make()
                ->danger()
                ->title('Remarks is required')
                ->body('Please fill Remarks before saving this report snapshot.')
                ->send();

            return;
        }

        $partNumbers = PartNumber::query()
            ->whereHas('activities')
            ->with([
                'qtyBudgets' => fn ($q) => $q->where('year', $year),
                'qtyForecasts' => fn ($q) => $q->where('year', $year),
            ])
            ->get();

        $savedRows = 0;

        foreach ($partNumbers as $partNumber) {
            foreach (self::months() as $month) {
                $budgetQty = $this->resolveMonthlyBudgetQty($partNumber, $month);
                $actOlQty = $this->resolveMonthlyActOLQty($partNumber, $month);
                $sourceType = $this->isBudgetActOlSelection($month) ? 'budget' : 'forecast';

                ReportMonthlyTransaction::updateOrCreate(
                    [
                        'part_number_id' => $partNumber->id,
                        'month' => $month,
                        'year' => $year,
                        'remarks' => $remarks,
                    ],
                    [
                        'qty_budget' => $budgetQty ?: null,
                        'qty_forecast' => $actOlQty ?: null,
                        'source_type' => $sourceType,
                        'created_by' => auth()->id(),
                    ]
                );

                $savedRows++;
            }
        }

        Notification::make()
            ->success()
            ->title('Report saved successfully')
            ->body("Saved {$savedRows} transaction row(s) for {$year} with remarks: {$remarks}.")
            ->send();
    }

    public static function months(): array
    {
        return [
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
            'January',
            'February',
            'March',
        ];
    }

    public function resolveMonthlyBudgetQty(PartNumber $partNumber, string $month): int
    {
        $year = (int) ($this->data['year'] ?? 0);

        if ($year <= 0) {
            return 0;
        }

        $aliases = self::monthAliases($month);

        return (int) $partNumber->qtyBudgets
            ->filter(fn ($row) => in_array((string) $row->month, $aliases, true))
            ->where('year', $year)
            ->sum('qty');
    }

    public function resolveMonthlyForecastQty(PartNumber $partNumber, string $month): int
    {
        $year = (int) ($this->data['year'] ?? 0);

        if ($year <= 0) {
            return 0;
        }

        $selectedUpdateQtyMonthId = $this->getSelectedUpdateQtyMonthIdForMonth($month);

        if (!$selectedUpdateQtyMonthId) {
            return 0;
        }

        $aliases = self::monthAliases($month);

        return (int) $partNumber->qtyForecasts
            ->where('update_qty_month_id', $selectedUpdateQtyMonthId)
            ->filter(fn ($row) => in_array((string) $row->month, $aliases, true))
            ->where('year', $year)
            ->sum('qty');
    }

    public function resolveActivityOBG(Activity $activity, PartNumber $partNumber, string $month): int
    {
        $qty = $this->resolveMonthlyBudgetQty($partNumber, $month);
        $crSatuan = $this->normalizeToNumber($activity->cr_satuan ?? null);

        return $qty * $crSatuan;
    }

    public function resolveActivityActOL(Activity $activity, PartNumber $partNumber, string $month): int
    {
        $crSatuan = $this->normalizeToNumber($activity->cr_satuan ?? null);
        $qty = $this->resolveMonthlyActOLQty($partNumber, $month);

        return $qty * $crSatuan;
    }

    public function resolveMonthlyActOLQty(PartNumber $partNumber, string $month): int
    {
        if ($this->isBudgetActOlSelection($month)) {
            return $this->resolveMonthlyBudgetQty($partNumber, $month);
        }

        return $this->resolveMonthlyForecastQty($partNumber, $month);
    }

    public function isBudgetActOlSelection(string $month): bool
    {
        $key = 'update_qty_month_id_' . strtolower($month);

        return ($this->data[$key] ?? null) === 'budget';
    }

    public function getSourceForMonth(string $month): string
    {
        return 'both';
    }

    public function getSelectedUpdateQtyMonthIdForMonth(string $month): ?int
    {
        $key = 'update_qty_month_id_' . strtolower($month);
        $value = $this->data[$key] ?? null;

        if ($value === null || $value === 'budget') {
            return null;
        }

        return (int) $value ?: null;
    }

    public function getSelectedUpdateQtyMonthLabel(string $month): ?string
    {
        $key = 'update_qty_month_id_' . strtolower($month);
        $selection = $this->data[$key] ?? null;

        if ($selection === 'budget') {
            return 'Qty Budget';
        }

        $id = $this->getSelectedUpdateQtyMonthIdForMonth($month);

        if (!$id) {
            return null;
        }

        $record = UpdateQtyMonth::find($id);

        if (!$record) {
            return null;
        }

        return "{$record->month} {$record->year} #{$record->id}";
    }

    protected static function monthAliases(string $month): array
    {
        $aliases = [
            'April' => ['April', 'Apr'],
            'May' => ['May'],
            'June' => ['June', 'Jun'],
            'July' => ['July', 'Jul'],
            'August' => ['August', 'Aug'],
            'September' => ['September', 'Sep'],
            'October' => ['October', 'Oct'],
            'November' => ['November', 'Nov'],
            'December' => ['December', 'Dec'],
            'January' => ['January', 'Jan'],
            'February' => ['February', 'Feb'],
            'March' => ['March', 'Mar'],
        ];

        return $aliases[$month] ?? [$month];
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
