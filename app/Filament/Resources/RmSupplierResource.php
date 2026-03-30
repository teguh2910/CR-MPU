<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RmSupplierResource\Pages;
use App\Models\RmSupplier;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RmSupplierResource extends Resource
{
    protected static ?string $model = RmSupplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'RM Supplier';

    protected static ?string $modelLabel = 'RM Supplier';

    protected static ?string $pluralModelLabel = 'RM Suppliers';

    protected static ?int $navigationSort = 40;

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

                TextInput::make('rm_currency')
                    ->label('RM Currency')
                    ->required()
                    ->maxLength(20),

                TextInput::make('rm_basis_price')
                    ->label('RM Basis Price')
                    ->numeric()
                    ->required()
                    ->minValue(0),

                TextInput::make('rm_weight_gram')
                    ->label('RM Weight (Gram)')
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

                TextColumn::make('period_from')
                    ->label('Period From')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('period_to')
                    ->label('Period To')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('rm_currency')
                    ->label('RM Currency')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('rm_basis_price')
                    ->label('RM Basis Price')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('rm_weight_gram')
                    ->label('RM Weight (Gram)')
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
            'index' => Pages\ListRmSuppliers::route('/'),
            'create' => Pages\CreateRmSupplier::route('/create'),
            'edit' => Pages\EditRmSupplier::route('/{record}/edit'),
        ];
    }
}
