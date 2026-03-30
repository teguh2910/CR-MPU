<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportMonthlyTransactionResource\Pages;
use App\Models\ReportMonthlyTransaction;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReportMonthlyTransactionResource extends Resource
{
    protected static ?string $model = ReportMonthlyTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Report';

    protected static ?string $navigationLabel = 'Monthly Report CR History';

    protected static ?string $modelLabel = 'Monthly Report Transaction';

    protected static ?string $pluralModelLabel = 'Monthly Report Transactions';

    protected static ?int $navigationSort = 20;

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->whereIn('id', function ($subQuery) {
                    $subQuery->from('report_monthly_transactions')
                        ->selectRaw('MIN(id)')
                        ->whereNotNull('remarks')
                        ->where('remarks', '!=', '')
                        ->groupBy('remarks', 'year');
                }))
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),

                TextColumn::make('remarks')
                    ->label('Remarks')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('show')
                    ->label('Show')
                    ->getStateUsing(fn () => 'Show')
                    ->icon('heroicon-o-eye')
                    ->url(fn (ReportMonthlyTransaction $record): string => static::getUrl('show', [
                        'year' => (int) $record->year,
                        'remarksKey' => static::encodeRemarksKey((string) $record->remarks),
                    ]))
                    ->color('primary'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('delete_snapshot')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete Snapshot')
                    ->modalDescription('This will delete all monthly report rows for this remarks and year.')
                    ->action(function (ReportMonthlyTransaction $record): void {
                        ReportMonthlyTransaction::query()
                            ->where('year', (int) $record->year)
                            ->where('remarks', (string) $record->remarks)
                            ->delete();
                    })
                    ->successNotificationTitle('Snapshot deleted'),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReportMonthlyTransactions::route('/'),
            'show' => Pages\ShowReportMonthlyTransaction::route('/show/{year}/{remarksKey}'),
        ];
    }

    public static function encodeRemarksKey(string $remarks): string
    {
        return rtrim(strtr(base64_encode($remarks), '+/', '-_'), '=');
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
