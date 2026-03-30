<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProcessCostSupplierResource\Pages;
use App\Models\ProcessCostSupplier;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProcessCostSupplierResource extends Resource
{
    protected static ?string $model = ProcessCostSupplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Process Cost Supplier';

    protected static ?string $modelLabel = 'Process Cost Supplier';

    protected static ?string $pluralModelLabel = 'Process Cost Suppliers';

    protected static ?int $navigationSort = 42;

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

                TextInput::make('process_cost_total')
                    ->label('Process Cost Total')
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

                TextColumn::make('process_cost_total')
                    ->label('Process Cost Total')
                    ->numeric(decimalPlaces: 0)
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
            'index' => Pages\ListProcessCostSuppliers::route('/'),
            'create' => Pages\CreateProcessCostSupplier::route('/create'),
            'edit' => Pages\EditProcessCostSupplier::route('/{record}/edit'),
        ];
    }
}
