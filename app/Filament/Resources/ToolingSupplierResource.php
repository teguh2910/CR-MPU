<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ToolingSupplierResource\Pages;
use App\Models\ToolingSupplier;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ToolingSupplierResource extends Resource
{
    protected static ?string $model = ToolingSupplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Tooling Supplier';

    protected static ?string $modelLabel = 'Tooling Supplier';

    protected static ?string $pluralModelLabel = 'Tooling Suppliers';

    protected static ?int $navigationSort = 43;

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

                TextInput::make('tooling_price')
                    ->label('Tooling Price')
                    ->numeric()
                    ->required()
                    ->minValue(0),

                TextInput::make('depre_per_pcs')
                    ->label('Depre/Pcs')
                    ->numeric()
                    ->required()
                    ->minValue(0),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('active')
                    ->required(),
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

                TextColumn::make('tooling_price')
                    ->label('Tooling Price')
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),

                TextColumn::make('depre_per_pcs')
                    ->label('Depre/Pcs')
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'active' ? 'success' : 'gray')
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
            'index' => Pages\ListToolingSuppliers::route('/'),
            'create' => Pages\CreateToolingSupplier::route('/create'),
            'edit' => Pages\EditToolingSupplier::route('/{record}/edit'),
        ];
    }
}
