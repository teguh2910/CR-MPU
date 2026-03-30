<?php

namespace App\Filament\Resources\ExRateResource\Pages;

use App\Filament\Resources\ExRateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExRate extends EditRecord
{
    protected static string $resource = ExRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
