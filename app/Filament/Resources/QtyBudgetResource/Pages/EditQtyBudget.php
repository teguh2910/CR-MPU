<?php

namespace App\Filament\Resources\QtyBudgetResource\Pages;

use App\Filament\Resources\QtyBudgetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQtyBudget extends EditRecord
{
    protected static string $resource = QtyBudgetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
