<?php

namespace App\Filament\Resources\OtherCostSupplierResource\Pages;

use App\Filament\Resources\OtherCostSupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOtherCostSupplier extends EditRecord
{
    protected static string $resource = OtherCostSupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
