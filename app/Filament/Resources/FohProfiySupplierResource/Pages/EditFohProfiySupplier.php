<?php

namespace App\Filament\Resources\FohProfiySupplierResource\Pages;

use App\Filament\Resources\FohProfiySupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFohProfiySupplier extends EditRecord
{
    protected static string $resource = FohProfiySupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
