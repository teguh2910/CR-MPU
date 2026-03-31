<?php

namespace App\Filament\Pages;

use App\Models\PartNumber;
use App\Models\Supplier;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;

class PriceConfirmationLetter extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Transaction';

    protected static ?string $navigationLabel = 'Price Confirmation Letter';

    protected static ?int $navigationSort = 50;

    protected static string $view = 'filament.pages.price-confirmation-letter';

    public ?array $data = [];

    public function mount(): void
    {
        $today = now();
        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $romanMonth = $romans[$today->month];
        $year = $today->year;
        
        $this->form->fill([
            'date' => $today->toDateString(),
            'letter_no' => "001/AII/PUR/{$romanMonth}/{$year}",
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Letter Header')
                    ->columns(2)
                    ->schema([
                        TextInput::make('letter_no')
                            ->label('Letter Number')
                            ->required(),
                        DatePicker::make('date')
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                if (!$state) return;
                                
                                $date = \Carbon\Carbon::parse($state);
                                $romans = [
                                    1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
                                    7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
                                ];
                                $romanMonth = $romans[$date->month];
                                $year = $date->year;
                                
                                $currentNo = explode('/', $get('letter_no'))[0] ?? '001';
                                $set('letter_no', "{$currentNo}/AII/PUR/{$romanMonth}/{$year}");
                            }),
                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->options(Supplier::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $supplier = Supplier::find($state);
                                if ($supplier) {
                                    $set('supplier_name', $supplier->name);
                                    $set('supplier_address', $supplier->address);
                                    $set('supplier_telp', $supplier->contact_phone);
                                    $set('attention', $supplier->contact_name);
                                }
                            }),
                        TextInput::make('supplier_name')
                            ->label('Supplier Name (for template)')
                            ->required(),
                        TextInput::make('supplier_address')
                            ->label('Supplier Address')
                            ->required(),
                        TextInput::make('supplier_telp')
                            ->label('Supplier Telp')
                            ->required(),
                        TextInput::make('attention')
                            ->label('Attention')
                            ->placeholder('e.g. Sales Manager'),
                        TextInput::make('cc')
                            ->label('CC')
                            ->placeholder('e.g. Finance, Purchasing'),
                    ]),

                Section::make('Period Information')
                    ->columns(3)
                    ->schema([
                        Select::make('period')
                            ->label('Current Period')
                            ->options(function () {
                                $year = date('Y');
                                $nextYear = $year + 1;
                                $prevYear = $year - 1;
                                
                                $periods = [];
                                foreach ([$prevYear, $year, $nextYear] as $y) {
                                    $periods["April {$y} - September {$y}"] = "April {$y} - September {$y}";
                                    $nextY = $y + 1;
                                    $periods["October {$y} - March {$nextY}"] = "October {$y} - March {$nextY}";
                                    $periods["April {$y} - June {$y}"] = "April {$y} - June {$y}";
                                    $periods["July {$y} - September {$y}"] = "July {$y} - September {$y}";
                                    $periods["October {$y} - December {$y}"] = "October {$y} - December {$y}";
                                    $periods["January {$y} - March {$y}"] = "January {$y} - March {$y}";
                                }
                                return $periods;
                            })
                            ->searchable()
                            ->required(),
                        Select::make('old_period')
                            ->label('Old Period')
                            ->options(function () {
                                $year = date('Y');
                                $periods = [];
                                for ($i = -2; $i <= 1; $i++) {
                                    $y = $year + $i;
                                    $shortY = substr($y, -2);
                                    
                                    // Half Yearly
                                    $periods["Apr-Sept {$shortY}"] = "Apr-Sept {$shortY}";
                                    $nextY = $y + 1;
                                    $shortNextY = substr($nextY, -2);
                                    $periods["Oct {$shortY} - Mar {$shortNextY}"] = "Oct {$shortY} - Mar {$shortNextY}";

                                    // Quarterly
                                    $periods["Jan-Mar {$shortY}"] = "Jan-Mar {$shortY}";
                                    $periods["Apr-Jun {$shortY}"] = "Apr-Jun {$shortY}";
                                    $periods["July-Sept {$shortY}"] = "July-Sept {$shortY}";
                                    $periods["Oct-Dec {$shortY}"] = "Oct-Dec {$shortY}";
                                }
                                return $periods;
                            })
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (!$state) return;

                                // Half-Yearly Logic
                                if (preg_match('/^Apr-Sept (\d{2})$/', $state, $matches)) {
                                    $year = $matches[1];
                                    $nextYear = str_pad(($year + 1) % 100, 2, '0', STR_PAD_LEFT);
                                    $set('new_period', "Oct {$year} - Mar {$nextYear}");
                                } elseif (preg_match('/^Oct (\d{2}) - Mar (\d{2})$/', $state, $matches)) {
                                    $nextYear = $matches[2];
                                    $set('new_period', "Apr-Sept {$nextYear}");
                                }
                                
                                // Quarterly Logic
                                elseif (preg_match('/^Jan-Mar (\d{2})$/', $state, $matches)) {
                                    $set('new_period', "Apr-Jun {$matches[1]}");
                                } elseif (preg_match('/^Apr-Jun (\d{2})$/', $state, $matches)) {
                                    $set('new_period', "July-Sept {$matches[1]}");
                                } elseif (preg_match('/^July-Sept (\d{2})$/', $state, $matches)) {
                                    $set('new_period', "Oct-Dec {$matches[1]}");
                                } elseif (preg_match('/^Oct-Dec (\d{2})$/', $state, $matches)) {
                                    $year = $matches[1];
                                    $nextYear = str_pad(($year + 1) % 100, 2, '0', STR_PAD_LEFT);
                                    $set('new_period', "Jan-Mar {$nextYear}");
                                }
                            }),
                        Select::make('new_period')
                            ->label('New Period')
                            ->options(function () {
                                $year = date('Y');
                                $periods = [];
                                for ($i = -1; $i <= 2; $i++) {
                                    $y = $year + $i;
                                    $shortY = substr($y, -2);
                                    
                                    // Half Yearly
                                    $periods["Apr-Sept {$shortY}"] = "Apr-Sept {$shortY}";
                                    $nextY = $y + 1;
                                    $shortNextY = substr($nextY, -2);
                                    $periods["Oct {$shortY} - Mar {$shortNextY}"] = "Oct {$shortY} - Mar {$shortNextY}";

                                    // Quarterly
                                    $periods["Jan-Mar {$shortY}"] = "Jan-Mar {$shortY}";
                                    $periods["Apr-Jun {$shortY}"] = "Apr-Jun {$shortY}";
                                    $periods["July-Sept {$shortY}"] = "July-Sept {$shortY}";
                                    $periods["Oct-Dec {$shortY}"] = "Oct-Dec {$shortY}";
                                }
                                return $periods;
                            })
                            ->searchable()
                            ->required(),
                    ]),

                Section::make('Material & Rate Information')
                    ->columns(3)
                    ->schema([
                        TextInput::make('rate')
                            ->label('Current Rate')
                            ->required(),
                        TextInput::make('old_rate')
                            ->label('Old Rate')
                            ->required(),
                        TextInput::make('new_rate')
                            ->label('New Rate')
                            ->required(),
                        TextInput::make('old_material')
                            ->label('Old Material Price')
                            ->required(),
                        TextInput::make('new_material')
                            ->label('New Material Price')
                            ->required(),
                    ]),

                Section::make('Price Details')
                    ->schema([
                        Repeater::make('items')
                            ->schema([
                                Select::make('part_number_id')
                                    ->label('Part Number')
                                    ->options(function (callable $get) {
                                        $supplierId = $get('../../supplier_id');
                                        if (!$supplierId) {
                                            return PartNumber::pluck('part_no', 'id');
                                        }
                                        return PartNumber::where('supplier_id', $supplierId)->pluck('part_no', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $part = PartNumber::find($state);
                                        if ($part) {
                                            $set('part_no', $part->part_no);
                                            $set('part_name', $part->part_name);
                                        }
                                    }),
                                TextInput::make('part_no')
                                    ->label('Part No (for template)')
                                    ->required(),
                                TextInput::make('part_name')
                                    ->label('Part Name')
                                    ->required(),
                                TextInput::make('old_price')
                                    ->numeric()
                                    ->required(),
                                TextInput::make('new_price')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->columns(3)
                            ->itemLabel(fn (array $state): ?string => $state['part_name'] ?? null)
                            ->collapsible()
                            ->defaultItems(1),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Print using Template')
                ->color('primary')
                ->icon('heroicon-o-printer')
                ->action('exportToWord'),
        ];
    }

    public function exportToWord()
    {
        $formData = $this->form->getState();

        // Template file MUST be .docx and stored in storage/app/templates/
        $templatePath = storage_path('app/templates/price_confirmation_template.docx');

        if (!file_exists($templatePath)) {
            \Filament\Notifications\Notification::make()
                ->title('Template not found')
                ->body('Please upload your .docx template to: storage/app/templates/price_confirmation_template.docx')
                ->danger()
                ->send();
            return;
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // Standardizing variables to support BOTH {{variable}} and ${variable}
        // PHPWord's TemplateProcessor::setValue(search, replace) matches literal strings.
        $variables = [
            'letter_no' => $formData['letter_no'],
            'date' => date('d F Y', strtotime($formData['date'])),
            'supplier_name' => $formData['supplier_name'],
            'supplier_address' => $formData['supplier_address'],
            'supplier_telp' => $formData['supplier_telp'],
            'period' => $formData['period'],
            'old_period' => $formData['old_period'],
            'new_period' => $formData['new_period'],
            'rate' => $formData['rate'],
            'old_rate' => $formData['old_rate'],
            'new_rate' => $formData['new_rate'],
            'old_material' => $formData['old_material'],
            'new_material' => $formData['new_material'],
            'attention' => $formData['attention'] ?? '-',
            'cc' => $formData['cc'] ?? '-',
        ];

        foreach ($variables as $key => $value) {
            // Try matching {{key}}
            $templateProcessor->setValue('{{' . $key . '}}', $value);
            // Try matching ${key} (Standard PHPWord)
            $templateProcessor->setValue($key, $value);
        }

        // Table row cloning: Create a row in your Word table with placeholder ${part_no}
        $items = [];
        $no = 1;
        foreach ($formData['items'] as $item) {
            $items[] = [
                'no' => $no++,
                'part_no' => $item['part_no'],
                'part_name' => $item['part_name'],
                'old_price' => number_format($item['old_price'], 2, ',', '.'),
                'new_price' => number_format($item['new_price'], 2, ',', '.'),
            ];
        }

        // cloneRowAndSetValues strictly requires ${} format for row placeholders in the document
        $templateProcessor->cloneRowAndSetValues('part_no', $items);

        $fileName = 'Price_Confirmation_' . str_replace('/', '_', $formData['letter_no']) . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'phpword');
        $templateProcessor->saveAs($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
