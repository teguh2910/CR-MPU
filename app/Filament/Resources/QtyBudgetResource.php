<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QtyBudgetResource\Pages;
use App\Models\QtyBudget;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class QtyBudgetResource extends Resource
{
    protected static ?string $model = QtyBudget::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Qty Budget';

    protected static ?string $pluralLabel = 'Qty Budgets';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQtyBudgets::route('/'),
            'create' => Pages\CreateQtyBudget::route('/create'),
            'edit' => Pages\EditQtyBudget::route('/{record}/edit'),
        ];
    }
}
