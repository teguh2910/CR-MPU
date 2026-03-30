<?php

namespace App\Filament\Pages;

use App\Filament\Resources\RfqResource;
use App\Models\Rfq;
use App\Models\Supplier;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class RequestForQuotation extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Transaction';

    protected static ?string $navigationLabel = 'Request For Quotation';

    protected static ?int $navigationSort = 11;

    protected static string $view = 'filament.pages.request-for-quotation';

    public ?array $data = [];

    public ?int $rfqId = null;

    public function mount(): void
    {
        $this->rfqId = request()->integer('rfq') ?: null;

        if ($this->rfqId) {
            $rfq = Rfq::query()->with(['items', 'exchangeRates'])->find($this->rfqId);

            if ($rfq) {
                $this->form->fill([
                    'to_company' => $rfq->to_company,
                    'to_pic' => $rfq->to_pic,
                    'rfq_date' => optional($rfq->rfq_date)->toDateString(),
                    'from_company' => $rfq->from_company,
                    'from_department' => $rfq->from_department,
                    'from_pic' => $rfq->from_pic,
                    'tel' => $rfq->tel,
                    'email' => $rfq->email,
                    'model' => $rfq->model,
                    'customer' => $rfq->customer,
                    'product_name' => $rfq->product_name,
                    'standard_qty' => $rfq->standard_qty,
                    'drawing_timing' => $rfq->drawing_timing,
                    'ots_target' => optional($rfq->ots_target)->toDateString(),
                    'otop_target' => optional($rfq->otop_target)->toDateString(),
                    'sop' => $rfq->sop,
                    'target_note' => $rfq->target_note,
                    'part_items' => $rfq->items->map(fn ($item) => [
                        'part_number' => $item->part_number,
                        'part_name' => $item->part_name,
                        'qty_mon' => $item->qty_mon,
                    ])->toArray(),
                    'quotation_due_date' => optional($rfq->quotation_due_date)->toDateString(),
                    'delivery_location' => $rfq->delivery_location,
                    'price_incoterm' => $rfq->price_incoterm,
                    'tooling_payment_method' => $rfq->tooling_payment_method,
                    'raw_material_period' => $rfq->raw_material_period,
                    'material_type' => $rfq->material_type,
                    'material_cps_price' => $rfq->material_cps_price,
                    'exchange_period' => $rfq->exchange_period,
                    'exchange_rates' => $rfq->exchangeRates->map(fn ($rate) => [
                        'currency' => $rate->currency,
                        'rate' => $rate->rate,
                    ])->toArray(),
                ]);

                return;
            }
        }

        $this->form->fill([
            'to_company' => 'PT Sukses Cipta Makmur',
            'to_pic' => 'Mr Wing - Marketing Team',
            'rfq_date' => now()->toDateString(),
            'from_company' => 'PT AISIN INDONESIA',
            'from_department' => 'Part Purchasing Dept.',
            'from_pic' => 'Fernanda S. Irawan',
            'tel' => '+62-813-50140417',
            'email' => 'fernanda@aisin-indonesia.co.id',
            'model' => 'OGAWA',
            'customer' => '-',
            'product_name' => 'Clutch Cover',
            'standard_qty' => 'As Table',
            'drawing_timing' => 'G-Drawing',
            'ots_target' => null,
            'otop_target' => null,
            'sop' => 'TBC',
            'target_note' => "RFQ for Subcont Process Machining + Balancing\n\nPlease submit quotation with detail information:\n1. Rate/min each OP\n2. Cycle Time each OP\n3. Jig Depreciation\n4. Scrapp Calculation",
            'part_items' => [
                [
                    'part_number' => '321121-30270-D1',
                    'part_name' => 'Pressure Plate - Mach',
                    'qty_mon' => '5.741',
                ],
            ],
            'quotation_due_date' => now()->addDays(10)->toDateString(),
            'delivery_location' => 'PT AISIN INDONESIA (Aii)',
            'price_incoterm' => 'DPP Aii',
            'tooling_payment_method' => 'Depreciation',
            'raw_material_period' => '2025-2ndhalf (Oct-2025-Mar-2026)',
            'material_type' => 'Non CPS',
            'material_cps_price' => '-',
            'exchange_period' => '2025-2ndhalf (Oct-2025-Mar-2026)',
            'exchange_rates' => [
                ['currency' => 'USD', 'rate' => '16,407'],
                ['currency' => 'JPY', 'rate' => '110,54'],
                ['currency' => 'THB', 'rate' => '489,65'],
            ],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('to_company')
                    ->label('To Company')
                    ->options(fn (): array => Supplier::query()->orderBy('name')->pluck('name', 'name')->toArray())
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required(),
                TextInput::make('to_pic')->label('To PIC')->required()->maxLength(255)->live(debounce: 300),
                DatePicker::make('rfq_date')->label('Date')->native(false)->live()->required(),

                TextInput::make('from_company')->label('From Company')->required()->maxLength(255)->live(debounce: 300),
                Select::make('from_department')
                    ->label('From Department')
                    ->options([
                        'Part Purchasing Dept.' => 'Part Purchasing Dept.',
                        'Material Purchasing Dept.' => 'Material Purchasing Dept.',
                    ])
                    ->live()
                    ->required(),
                TextInput::make('from_pic')->label('From PIC')->required()->maxLength(255)->live(debounce: 300),
                TextInput::make('tel')->label('Telephone')->maxLength(50)->live(debounce: 300),
                TextInput::make('email')->label('Email')->email()->maxLength(255)->live(debounce: 300),

                TextInput::make('model')->label('Model')->required()->maxLength(100)->live(debounce: 300),
                TextInput::make('customer')->label('Customer')->maxLength(100)->live(debounce: 300),
                TextInput::make('product_name')->label('Product Name')->required()->maxLength(255)->live(debounce: 300),
                TextInput::make('standard_qty')->label('Standard Qty')->required()->maxLength(100)->live(debounce: 300),
                TextInput::make('drawing_timing')->label('Drawing Timing')->maxLength(100)->live(debounce: 300),
                DatePicker::make('ots_target')->label('OTS Target')->native(false)->live(),
                DatePicker::make('otop_target')->label('OTOP Target')->native(false)->live(),
                TextInput::make('sop')->label('SOP')->maxLength(100)->live(debounce: 300),

                Repeater::make('part_items')
                    ->label('Target Parts')
                    ->schema([
                        TextInput::make('part_number')->required()->maxLength(100)->live(debounce: 300),
                        TextInput::make('part_name')->required()->maxLength(255)->live(debounce: 300),
                        TextInput::make('qty_mon')->required()->maxLength(100)->live(debounce: 300),
                    ])
                    ->defaultItems(1)
                    ->columns(3)
                    ->reorderable(false)
                    ->live()
                    ->addActionLabel('Add Part Row')
                    ->columnSpanFull(),

                Textarea::make('target_note')
                    ->label('Target Notes')
                    ->rows(5)
                    ->live(debounce: 300)
                    ->columnSpanFull(),

                DatePicker::make('quotation_due_date')->label('Quotation Due Date')->native(false)->live(),

                TextInput::make('delivery_location')->label('Delivery Location')->maxLength(255)->live(debounce: 300),
                TextInput::make('price_incoterm')->label('Price Incoterm')->maxLength(100)->live(debounce: 300),
                Select::make('tooling_payment_method')
                    ->label('Tooling Payment Method')
                    ->options([
                        'Depreciation' => 'Depreciation',
                        'One Time Payment' => 'One Time Payment',
                    ])
                    ->live()
                    ->required(),
                TextInput::make('raw_material_period')->label('Raw Material Period')->maxLength(255)->live(debounce: 300),
                TextInput::make('material_type')->label('Material AISIN CPS or Non-CPS')->maxLength(100)->live(debounce: 300),
                TextInput::make('material_cps_price')->label('Material AISIN CPS Price')->maxLength(100)->live(debounce: 300),
                TextInput::make('exchange_period')->label('Exchange Rate Period')->maxLength(255)->live(debounce: 300),

                Repeater::make('exchange_rates')
                    ->label('Exchange Rates')
                    ->schema([
                        TextInput::make('currency')->required()->maxLength(10)->live(debounce: 300),
                        TextInput::make('rate')->required()->maxLength(50)->live(debounce: 300),
                    ])
                    ->defaultItems(1)
                    ->columns(2)
                    ->reorderable(false)
                    ->live()
                    ->addActionLabel('Add Currency')
                    ->columnSpanFull(),
            ])
            ->columns(3)
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        DB::transaction(function () use ($state): void {
            $rfq = $this->rfqId
                ? Rfq::query()->findOrFail($this->rfqId)
                : new Rfq();

            $rfq->fill([
                'to_company' => $state['to_company'],
                'to_pic' => $state['to_pic'],
                'rfq_date' => $state['rfq_date'],
                'from_company' => $state['from_company'],
                'from_department' => $state['from_department'],
                'from_pic' => $state['from_pic'],
                'tel' => $state['tel'] ?? null,
                'email' => $state['email'] ?? null,
                'model' => $state['model'],
                'customer' => $state['customer'] ?? null,
                'product_name' => $state['product_name'],
                'standard_qty' => $state['standard_qty'],
                'drawing_timing' => $state['drawing_timing'] ?? null,
                'ots_target' => $state['ots_target'] ?? null,
                'otop_target' => $state['otop_target'] ?? null,
                'sop' => $state['sop'] ?? null,
                'target_note' => $state['target_note'] ?? null,
                'quotation_due_date' => $state['quotation_due_date'] ?? null,
                'delivery_location' => $state['delivery_location'] ?? null,
                'price_incoterm' => $state['price_incoterm'] ?? null,
                'tooling_payment_method' => $state['tooling_payment_method'] ?? null,
                'raw_material_period' => $state['raw_material_period'] ?? null,
                'material_type' => $state['material_type'] ?? null,
                'material_cps_price' => $state['material_cps_price'] ?? null,
                'exchange_period' => $state['exchange_period'] ?? null,
            ]);

            if (! $rfq->exists) {
                $rfq->created_by = auth()->id();
                $rfq->status = 'draft';
            }

            $rfq->save();

            $rfq->items()->delete();
            $rfq->exchangeRates()->delete();

            foreach (($state['part_items'] ?? []) as $index => $item) {
                if (blank($item['part_number'] ?? null) && blank($item['part_name'] ?? null) && blank($item['qty_mon'] ?? null)) {
                    continue;
                }

                $rfq->items()->create([
                    'part_number' => (string) ($item['part_number'] ?? ''),
                    'part_name' => (string) ($item['part_name'] ?? ''),
                    'qty_mon' => (string) ($item['qty_mon'] ?? ''),
                    'sort_order' => $index + 1,
                ]);
            }

            foreach (($state['exchange_rates'] ?? []) as $index => $rate) {
                if (blank($rate['currency'] ?? null) && blank($rate['rate'] ?? null)) {
                    continue;
                }

                $rfq->exchangeRates()->create([
                    'currency' => (string) ($rate['currency'] ?? ''),
                    'rate' => (string) ($rate['rate'] ?? ''),
                    'sort_order' => $index + 1,
                ]);
            }
        });

        Notification::make()
            ->title($this->rfqId ? 'RFQ updated successfully' : 'RFQ saved successfully')
            ->success()
            ->send();

        $this->redirect(RfqResource::getUrl('index'));
    }
}
