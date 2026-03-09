<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CrReportResource\Pages;
use App\Models\PartNumber;
use App\Models\UpdateQtyMonth;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class CrReportResource extends Resource
{
    protected static ?string $model = PartNumber::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationGroup = 'Report';

    protected static ?string $navigationLabel = 'CR';

    protected static ?string $modelLabel = 'CR Report';

    protected static ?string $pluralModelLabel = 'CR Reports';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                // Only show part numbers that have activities
                $query->whereHas('activities')
                    ->with(['supplier', 'product', 'category', 'activities', 'qtyForecasts.updateQtyMonth', 'qtyBudgets']);
            })
            ->columns([
                TextColumn::make('part_no')
                    ->label('Part Number')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('activities.activity')
                    ->label('Activity')
                    ->searchable()
                    ->limitList(2),

                TextColumn::make('activities.cr_no')
                    ->label('CR No')
                    ->searchable()
                    ->limitList(2),

                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('product.name')
                    ->label('Product')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('activities.cr_satuan')
                    ->label('CR/Satuan')
                    ->formatStateUsing(function ($state) {
                        $normalize = function ($value) {
                            if ($value === null || $value === '') {
                                return null;
                            }

                            $raw = preg_replace('/[^0-9,.-]/', '', (string) $value);
                            if ($raw === '') {
                                return (string) $value;
                            }

                            $hasComma = str_contains($raw, ',');
                            $hasDot = str_contains($raw, '.');

                            if ($hasComma && $hasDot) {
                                if (strrpos($raw, ',') > strrpos($raw, '.')) {
                                    // Example: 1.234,56 -> 1234.56
                                    $raw = str_replace('.', '', $raw);
                                    $raw = str_replace(',', '.', $raw);
                                } else {
                                    // Example: 1,234.56 -> 1234.56
                                    $raw = str_replace(',', '', $raw);
                                }
                            } elseif ($hasComma) {
                                // Example: 123,45 -> 123.45
                                $raw = str_replace(',', '.', $raw);
                            }

                            return (string) round((float) $raw);
                        };

                        if (is_array($state)) {
                            return array_map($normalize, $state);
                        }

                        return $normalize($state);
                    })
                    ->searchable()
                    ->limitList(2),

                TextColumn::make('activities.satuan')
                    ->label('Satuan')
                    ->searchable()
                    ->limitList(2),

                TextColumn::make('activities.plan_svp_month')
                    ->label('Plan SVP')
                    ->date('d M Y')
                    ->limitList(2),

                TextColumn::make('activities.act_svp_month')
                    ->label('Act SVP')
                    ->date('d M Y')
                    ->limitList(2),

                // Month columns - Fiscal Year (Apr to Mar)
                TextColumn::make('april_budget_amount')
                    ->label('Amount Budget Apr')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyBudgetAmount($record, 'April', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('april_forecast_amount')
                    ->label('Amount Qty Forecast Apr')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyForecastAmount($record, 'April', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('may_budget_amount')
                    ->label('Amount Budget May')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyBudgetAmount($record, 'May', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('may_forecast_amount')
                    ->label('Amount Qty Forecast May')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyForecastAmount($record, 'May', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('june_budget_amount')
                    ->label('Amount Budget Jun')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyBudgetAmount($record, 'June', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('june_forecast_amount')
                    ->label('Amount Qty Forecast Jun')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyForecastAmount($record, 'June', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('july_budget_amount')
                    ->label('Amount Budget Jul')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyBudgetAmount($record, 'July', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('july_forecast_amount')
                    ->label('Amount Qty Forecast Jul')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyForecastAmount($record, 'July', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('august_budget_amount')
                    ->label('Amount Budget Aug')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyBudgetAmount($record, 'August', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('august_forecast_amount')
                    ->label('Amount Qty Forecast Aug')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyForecastAmount($record, 'August', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('september_budget_amount')
                    ->label('Amount Budget Sep')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyBudgetAmount($record, 'September', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('september_forecast_amount')
                    ->label('Amount Qty Forecast Sep')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyForecastAmount($record, 'September', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('october_budget_amount')
                    ->label('Amount Budget Oct')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyBudgetAmount($record, 'October', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('october_forecast_amount')
                    ->label('Amount Qty Forecast Oct')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyForecastAmount($record, 'October', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('november_budget_amount')
                    ->label('Amount Budget Nov')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyBudgetAmount($record, 'November', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('november_forecast_amount')
                    ->label('Amount Qty Forecast Nov')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyForecastAmount($record, 'November', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('december_budget_amount')
                    ->label('Amount Budget Dec')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyBudgetAmount($record, 'December', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('december_forecast_amount')
                    ->label('Amount Qty Forecast Dec')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyForecastAmount($record, 'December', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('january_budget_amount')
                    ->label('Amount Budget Jan')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyBudgetAmount($record, 'January', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('january_forecast_amount')
                    ->label('Amount Qty Forecast Jan')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyForecastAmount($record, 'January', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('february_budget_amount')
                    ->label('Amount Budget Feb')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyBudgetAmount($record, 'February', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('february_forecast_amount')
                    ->label('Amount Qty Forecast Feb')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyForecastAmount($record, 'February', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('march_budget_amount')
                    ->label('Amount Budget Mar')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyBudgetAmount($record, 'March', $year);
                    })
                    ->alignCenter(),

                TextColumn::make('march_forecast_amount')
                    ->label('Amount Qty Forecast Mar')
                    ->getStateUsing(function (PartNumber $record, $livewire) {
                        $year = self::getSelectedYear($livewire);
                        return self::resolveMonthlyForecastAmount($record, 'March', $year);
                    })
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('update_qty_month_id')
                    ->label('Update Qty Month')
                    ->options(function () {
                        return UpdateQtyMonth::query()
                            ->orderByDesc('year')
                            ->orderBy('month')
                            ->get()
                            ->mapWithKeys(fn (UpdateQtyMonth $record) => [
                                $record->id => "{$record->month} {$record->year}",
                            ])
                            ->toArray();
                    })
                    ->searchable()
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('qtyForecasts', function (Builder $q) use ($data) {
                                $q->where('update_qty_month_id', $data['value']);
                            });
                        }
                    }),

                SelectFilter::make('year')
                    ->label('Year')
                    ->options(function () {
                        $years = [];
                        for ($i = 2020; $i <= 2030; $i++) {
                            $years[$i] = $i;
                        }
                        return $years;
                    })
                    ->default(date('Y'))
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('qtyForecasts', function (Builder $q) use ($data) {
                                $q->where('year', $data['value']);
                            });
                        }
                    }),
            ])
            ->actions([])
            ->bulkActions([])
            ->defaultSort('part_no', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    protected static function getSelectedYear($livewire): int
    {
        return (int) ($livewire->tableFilters['year']['value'] ?? date('Y'));
    }

    protected static function resolveMonthlyQty(PartNumber $record, string $month, int $year): string
    {
        $qty = self::resolveMonthlyQtyValue($record, $month, $year);

        return $qty > 0 ? number_format($qty) : '-';
    }

    protected static function resolveMonthlyQtyValue(PartNumber $record, string $month, int $year): int
    {
        $forecastQty = (int) $record->qtyForecasts()
            ->where('month', $month)
            ->where('year', $year)
            ->sum('qty');

        if ($forecastQty > 0) {
            return $forecastQty;
        }

        $budgetQty = (int) $record->qtyBudgets()
            ->where('month', $month)
            ->where('year', $year)
            ->sum('qty');

        return $budgetQty;
    }

    protected static function resolveMonthlyForecastAmount(PartNumber $record, string $month, int $year): string
    {
        $forecastQty = (int) $record->qtyForecasts()
            ->where('month', $month)
            ->where('year', $year)
            ->sum('qty');

        return $forecastQty > 0 ? number_format($forecastQty) : '-';
    }

    protected static function resolveMonthlyBudgetAmount(PartNumber $record, string $month, int $year): string
    {
        $budgetQty = (int) $record->qtyBudgets()
            ->where('month', $month)
            ->where('year', $year)
            ->sum('qty');

        return $budgetQty > 0 ? number_format($budgetQty) : '-';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCrReports::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
