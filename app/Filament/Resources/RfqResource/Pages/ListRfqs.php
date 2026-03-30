<?php

namespace App\Filament\Resources\RfqResource\Pages;

use App\Filament\Resources\RfqResource;
use App\Filament\Pages\RequestForQuotation;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRfqs extends ListRecords
{
    protected static string $resource = RfqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('new_rfq')
                ->label('Create RFQ')
                ->url(RequestForQuotation::getUrl())
                ->icon('heroicon-o-plus'),
        ];
    }
}
