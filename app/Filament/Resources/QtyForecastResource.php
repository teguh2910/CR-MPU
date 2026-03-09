<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QtyForecastResource\Pages;
use App\Models\QtyForecast;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class QtyForecastResource extends Resource
{
    protected static ?string $model = QtyForecast::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Qty Forecast';

    protected static ?string $pluralLabel = 'Qty Forecasts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('update_qty_month_id')
                    ->label('Update Qty Month')
                    ->relationship('updateQtyMonth', 'month')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('part_number_id')
                    ->label('Part Number')
                    ->relationship('partNumber', 'part_no')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('month')
                    ->required()
                    ->maxLength(255),

                TextInput::make('qty')
                    ->required()
                    ->numeric()
                    ->minValue(0),

                TextInput::make('year')
                    ->required()
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(2100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('updateQtyMonth.month')
                    ->label('Update Qty Month')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('partNumber.part_no')
                    ->label('Part Number')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('month')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('qty')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('year')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQtyForecasts::route('/'),
            'create' => Pages\CreateQtyForecast::route('/create'),
            'edit' => Pages\EditQtyForecast::route('/{record}/edit'),
        ];
    }
}
