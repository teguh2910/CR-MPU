<?php

namespace App\Filament\Resources\ProcessCostSupplierResource\Pages;

use App\Filament\Resources\ProcessCostSupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProcessCostSupplier extends EditRecord
{
    protected static string $resource = ProcessCostSupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
