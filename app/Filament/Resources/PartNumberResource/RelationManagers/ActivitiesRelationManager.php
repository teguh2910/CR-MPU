<?php

namespace App\Filament\Resources\PartNumberResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('cr_no')
                    ->label('CR No')
                    ->maxLength(255),

                TextInput::make('activity')
                    ->required()
                    ->maxLength(255),

                TextInput::make('year')
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(2100),

                TextInput::make('cr_satuan')
                    ->label('CR/Satuan')
                    ->maxLength(255),

                TextInput::make('satuan')
                    ->maxLength(255),

                DatePicker::make('plan_svp_month')
                    ->label('Plan SVP (Month)'),

                DatePicker::make('act_svp_month')
                    ->label('Act SVP (Month)'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('activity')
            ->columns([
                TextColumn::make('cr_no')
                    ->label('CR No')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('activity')
                    ->searchable(),

                TextColumn::make('year')
                    ->sortable(),

                TextColumn::make('cr_satuan')
                    ->label('CR/Satuan')
                    ->searchable(),

                TextColumn::make('satuan')
                    ->searchable(),

                TextColumn::make('plan_svp_month')
                    ->label('Plan SVP (Month)')
                    ->searchable(),

                TextCodate('d M Y')
                    ->sortable(),

                TextColumn::make('act_svp_month')
                    ->label('Act SVP (Month)')
                    ->date('d M Y')
                    ->sortmake('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
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
