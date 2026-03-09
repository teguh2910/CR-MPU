<?php

namespace App\Filament\Resources\UpdateQtyMonthResource\Pages;

use App\Filament\Resources\UpdateQtyMonthResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUpdateQtyMonths extends ListRecords
{
    protected static string $resource = UpdateQtyMonthResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
