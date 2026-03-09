<?php

namespace App\Filament\Resources\UpdateQtyMonthResource\Pages;

use App\Filament\Resources\UpdateQtyMonthResource;
use App\Imports\QtyForecastForUpdateQtyMonthImport;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class EditUpdateQtyMonth extends EditRecord
{
    protected static string $resource = UpdateQtyMonthResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('importExcel')
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
                        ->helperText('Supported headers: (1) part_no, month, qty, year or (2) part_no, year, jan..dec. update_qty_month_id is automatic.'),
                ])
                ->action(function (array $data): void {
                    try {
                        $storedFile = is_array($data['file']) ? reset($data['file']) : $data['file'];
                        $disk = config('filament.default_filesystem_disk', config('filesystems.default'));

                        if (!Storage::disk($disk)->exists($storedFile)) {
                            throw new \RuntimeException("Uploaded file not found on disk '{$disk}': {$storedFile}");
                        }

                        Excel::import(
                            new QtyForecastForUpdateQtyMonthImport($this->record->id),
                            Storage::disk($disk)->path($storedFile)
                        );

                        Notification::make()
                            ->title('Import Successful')
                            ->success()
                            ->body('Qty forecasts have been imported for this Update Qty Month.')
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Import Failed')
                            ->danger()
                            ->body('Error: ' . $e->getMessage())
                            ->send();
                    }
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
