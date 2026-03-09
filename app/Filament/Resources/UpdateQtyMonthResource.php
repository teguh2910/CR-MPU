<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UpdateQtyMonthResource\Pages;
use App\Filament\Resources\UpdateQtyMonthResource\RelationManagers\QtyForecastsRelationManager;
use App\Models\UpdateQtyMonth;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class UpdateQtyMonthResource extends Resource
{
    protected static ?string $model = UpdateQtyMonth::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Update Qty Month';

    protected static ?string $pluralLabel = 'Update Qty Months';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('month')
                    ->required()
                    ->maxLength(255),

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
                TextColumn::make('month')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('year')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            QtyForecastsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUpdateQtyMonths::route('/'),
            'create' => Pages\CreateUpdateQtyMonth::route('/create'),
            'edit' => Pages\EditUpdateQtyMonth::route('/{record}/edit'),
        ];
    }
}
