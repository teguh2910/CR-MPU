<?php

namespace App\Filament\Resources\CrReportResource\Pages;

use App\Filament\Resources\CrReportResource;
use Filament\Resources\Pages\ListRecords;

class ListCrReports extends ListRecords
{
    protected static string $resource = CrReportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
