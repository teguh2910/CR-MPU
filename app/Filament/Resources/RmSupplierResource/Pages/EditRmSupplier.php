<?php

namespace App\Filament\Resources\RmSupplierResource\Pages;

use App\Filament\Resources\RmSupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRmSupplier extends EditRecord
{
    protected static string $resource = RmSupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
