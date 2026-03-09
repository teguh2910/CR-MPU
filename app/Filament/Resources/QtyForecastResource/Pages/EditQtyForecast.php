<?php

namespace App\Filament\Resources\QtyForecastResource\Pages;

use App\Filament\Resources\QtyForecastResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQtyForecast extends EditRecord
{
    protected static string $resource = QtyForecastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
