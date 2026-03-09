<?php

namespace App\Filament\Resources\PartNumberResource\Pages;

use App\Filament\Resources\PartNumberResource;
use App\Imports\PartNumberImport;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ListPartNumbers extends ListRecords
{
    protected static string $resource = PartNumberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
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
                        ->helperText('Required headers: part_no, part_name, supplier, product, category. Optional: part_sap'),
                ])
                ->action(function (array $data) {
                    try {
                        $storedFile = is_array($data['file']) ? reset($data['file']) : $data['file'];
                        $disk = config('filament.default_filesystem_disk', config('filesystems.default'));

                        if (!Storage::disk($disk)->exists($storedFile)) {
                            throw new \RuntimeException("Uploaded file not found on disk '{$disk}': {$storedFile}");
                        }

                        Excel::import(new PartNumberImport(), Storage::disk($disk)->path($storedFile));
                        
                        Notification::make()
                            ->title('Import Successful')
                            ->success()
                            ->body('Part numbers have been imported successfully.')
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
