<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Models\Activity;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Master Data';

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
                    ->numeric()
                    ->maxLength(255),

                TextInput::make('satuan')
                    ->maxLength(255),

                DatePicker::make('plan_svp_month')
                    ->label('Plan SVP (Month)'),

                DatePicker::make('act_svp_month')
                    ->label('Act SVP (Month)'),
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

                TextColumn::make('cr_no')
                    ->label('CR No')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('activity')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('year')
                    ->sortable(),

                TextColumn::make('cr_satuan')
                    ->label('CR/Satuan')
                    ->numeric(decimalPlaces: 0)
                    ->searchable(),

                TextColumn::make('satuan')
                    ->searchable(),

                TextColumn::make('plan_svp_month')
                    ->label('Plan SVP (Month)')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('act_svp_month')
                    ->label('Act SVP (Month)')
                    ->date('d M Y')
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
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivity::route('/create'),
            'edit' => Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
