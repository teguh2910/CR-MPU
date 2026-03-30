<?php

namespace App\Filament\Resources\FohProfiySupplierResource\Pages;

use App\Exports\MissingPartNumbersExport;
use App\Filament\Resources\FohProfiySupplierResource;
use App\Imports\FohProfiySupplierImport;
use App\Imports\PartNoReaderImport;
use App\Models\PartNumber;
use Filament\Actions;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ListFohProfiySuppliers extends ListRecords
{
    protected static string $resource = FohProfiySupplierResource::class;

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
                        ->helperText('Required headers: part_no, percentage. Optional: remarks'),
                ])
                ->action(function (array $data) {
                    try {
                        $storedFile = is_array($data['file']) ? reset($data['file']) : $data['file'];
                        $disk = config('filament.default_filesystem_disk', config('filesystems.default'));

                        if (! Storage::disk($disk)->exists($storedFile)) {
                            throw new \RuntimeException("Uploaded file not found on disk '{$disk}': {$storedFile}");
                        }

                        $filePath = Storage::disk($disk)->path($storedFile);

                        $reader = new PartNoReaderImport();
                        Excel::import($reader, $filePath);
                        $partNos = $reader->rows->pluck('part_no')->filter()->unique()->values();
                        $existingPartNos = PartNumber::whereIn('part_no', $partNos)->pluck('part_no');
                        $missingPartNos = $partNos->diff($existingPartNos)->values();

                        if ($missingPartNos->isNotEmpty()) {
                            $filename = 'missing_part_numbers_' . now()->format('Ymd_His') . '.xlsx';
                            Excel::store(new MissingPartNumbersExport($missingPartNos->toArray()), $filename, 'public');
                            $downloadUrl = Storage::disk('public')->url($filename);

                            Notification::make()
                                ->title('Import Failed — Missing Part Numbers')
                                ->danger()
                                ->body("Found {$missingPartNos->count()} part number(s) not in master. Please create them first, then re-import.")
                                ->actions([
                                    NotificationAction::make('download')
                                        ->label('Download Missing List')
                                        ->url($downloadUrl)
                                        ->openUrlInNewTab()
                                        ->button(),
                                ])
                                ->persistent()
                                ->send();
                            return;
                        }

                        Excel::import(new FohProfiySupplierImport(), $filePath);

                        Notification::make()
                            ->title('Import Successful')
                            ->success()
                            ->body('FOH & Profiy Supplier data has been imported successfully.')
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
