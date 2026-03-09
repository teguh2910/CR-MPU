<?php

namespace App\Filament\Resources\UpdateQtyMonthResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class QtyForecastsRelationManager extends RelationManager
{
    protected static string $relationship = 'qtyForecasts';

    public function form(Form $form): Form
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('month')
            ->columns([
                TextColumn::make('partNumber.part_no')
                    ->label('Part Number')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('month')
                    ->searchable(),

                TextColumn::make('qty')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('year')
                    ->sortable(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
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
}
