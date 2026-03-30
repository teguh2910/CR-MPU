<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExRateResource\Pages;
use App\Models\ExRate;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExRateResource extends Resource
{
    protected static ?string $model = ExRate::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Ex-Rate';

    protected static ?string $modelLabel = 'Ex-Rate';

    protected static ?string $pluralModelLabel = 'Ex-Rates';

    protected static ?int $navigationSort = 41;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('currency')
                    ->label('Currency')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),

                DatePicker::make('period_from')
                    ->label('Period From')
                    ->native(false)
                    ->displayFormat('d M Y')
                    ->required(),

                DatePicker::make('period_to')
                    ->label('Period To')
                    ->native(false)
                    ->displayFormat('d M Y')
                    ->afterOrEqual('period_from')
                    ->required(),

                TextInput::make('rate')
                    ->label('Rate')
                    ->numeric()
                    ->required()
                    ->minValue(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('currency')
                    ->label('Currency')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('period_from')
                    ->label('Period From')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('period_to')
                    ->label('Period To')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('rate')
                    ->label('Rate')
                    ->numeric(decimalPlaces: 2)
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExRates::route('/'),
            'create' => Pages\CreateExRate::route('/create'),
            'edit' => Pages\EditExRate::route('/{record}/edit'),
        ];
    }
}
