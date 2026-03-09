<?php

namespace App\Filament\Resources\QtyForecastResource\Pages;

use App\Filament\Resources\QtyForecastResource;
use App\Exports\QtyForecastPivotTemplateExport;
use App\Exports\QtyForecastTemplateExport;
use App\Imports\QtyForecastImport;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ListQtyForecasts extends ListRecords
{
    protected static string $resource = QtyForecastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    return Excel::download(new QtyForecastTemplateExport, 'qty_forecast_template.xlsx');
                }),
            Actions\Action::make('downloadPivotTemplate')
                ->label('Download Pivot Template')
                ->icon('heroicon-o-table-cells')
                ->color('gray')
                ->action(function () {
                    return Excel::download(new QtyForecastPivotTemplateExport, 'qty_forecast_pivot_template.xlsx');
                }),
            Actions\Action::make('import')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    FileUpload::make('file')
                        ->label('Excel File')
                        ->acceptedFileTypes([
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'text/csv',
                        ])
                        ->required()
                        ->helperText('Supported headers: (1) update_qty_month_id, part_no, month, qty, year or (2) update_qty_month_id, part_no, year, jan..dec.'),
                ])
                ->action(function (array $data) {
                    try {
                        $storedFile = is_array($data['file']) ? reset($data['file']) : $data['file'];
                        $disk = config('filament.default_filesystem_disk', config('filesystems.default'));

                        if (!Storage::disk($disk)->exists($storedFile)) {
                            throw new \RuntimeException("Uploaded file not found on disk '{$disk}': {$storedFile}");
                        }

                        Excel::import(new QtyForecastImport(), Storage::disk($disk)->path($storedFile));
                        
                        Notification::make()
                            ->title('Import Successful')
                            ->success()
                            ->body('Qty forecasts have been imported successfully.')
                            ->send();
                            
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Import Failed')
                            ->danger()
                            ->body('Error: ' . $e->getMessage())
                            ->send();
                    }
                }),
        ];
    }
}
