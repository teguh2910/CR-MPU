<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartNumberResource\Pages;
use App\Filament\Resources\PartNumberResource\RelationManagers\ActivitiesRelationManager;
use App\Models\PartNumber;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class PartNumberResource extends Resource
{
    protected static ?string $model = PartNumber::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $modelLabel = 'Part Number';

    protected static ?string $pluralModelLabel = 'Part Numbers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Part Number Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('part_no')
                            ->label('Part No')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('part_sap')
                            ->label('Part SAP')
                            ->maxLength(255),

                        TextInput::make('part_name')
                            ->label('Part Name')
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255),

                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->relationship('supplier', 'name')
                            ->createOptionForm([
                                TextInput::make('name')->required()->maxLength(255),
                            ])
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'name')
                            ->createOptionForm([
                                TextInput::make('name')->required()->maxLength(255),
                            ])
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->createOptionForm([
                                TextInput::make('name')->required()->maxLength(255),
                            ])
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('part_no')
                    ->label('Part No')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('part_sap')
                    ->label('Part SAP')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('part_name')
                    ->label('Part Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('supplier')
                    ->relationship('supplier', 'name'),

                SelectFilter::make('product')
                    ->relationship('product', 'name'),

                SelectFilter::make('category')
                    ->relationship('category', 'name'),
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

    public static function getRelations(): array
    {
        return [
            ActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartNumbers::route('/'),
            'create' => Pages\CreatePartNumber::route('/create'),
            'edit' => Pages\EditPartNumber::route('/{record}/edit'),
        ];
    }
}
