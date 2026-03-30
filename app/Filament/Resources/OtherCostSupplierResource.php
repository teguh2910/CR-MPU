<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OtherCostSupplierResource\Pages;
use App\Models\OtherCostSupplier;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OtherCostSupplierResource extends Resource
{
    protected static ?string $model = OtherCostSupplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Other Cost Supplier';

    protected static ?string $modelLabel = 'Other Cost Supplier';

    protected static ?string $pluralModelLabel = 'Other Cost Suppliers';

    protected static ?int $navigationSort = 44;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('part_no_id')
                    ->label('Part No')
                    ->relationship('partNumber', 'part_no')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('remark')
                    ->label('Remark')
                    ->required()
                    ->maxLength(255),

                TextInput::make('cost')
                    ->label('Cost')
                    ->numeric()
                    ->required()
                    ->minValue(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('partNumber.part_no')
                    ->label('Part No')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('partNumber.part_name')
                    ->label('Part Name')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('remark')
                    ->label('Remark')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('cost')
                    ->label('Cost')
                    ->numeric(decimalPlaces: 4)
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
            'index' => Pages\ListOtherCostSuppliers::route('/'),
            'create' => Pages\CreateOtherCostSupplier::route('/create'),
            'edit' => Pages\EditOtherCostSupplier::route('/{record}/edit'),
        ];
    }
}
