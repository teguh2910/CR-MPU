<?php

namespace App\Filament\Resources\ToolingSupplierResource\Pages;

use App\Filament\Resources\ToolingSupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditToolingSupplier extends EditRecord
{
    protected static string $resource = ToolingSupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
