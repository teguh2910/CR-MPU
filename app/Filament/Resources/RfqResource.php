<?php

namespace App\Filament\Resources;

use App\Filament\Pages\RequestForQuotation;
use App\Filament\Resources\RfqResource\Pages;
use App\Mail\RfqMail;
use App\Models\Rfq;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class RfqResource extends Resource
{
    protected static ?string $model = Rfq::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Transaction';

    protected static ?string $navigationLabel = 'RFQ List';

    protected static ?int $navigationSort = 12;

    protected const STATUS_FLOW = [
        'draft' => ['draft', 'emailed'],
        'emailed' => ['emailed', 'wait quotation'],
        'wait quotation' => ['wait quotation', 'received quotation'],
        'received quotation' => ['received quotation'],
    ];

    public static function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('RFQ ID')
                    ->sortable(),

                TextColumn::make('to_company')
                    ->label('To Company')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('product_name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rfq_date')
                    ->label('RFQ Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('quotation_due_date')
                    ->label('Due Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'draft' => 'gray',
                        'emailed' => 'info',
                        'wait quotation' => 'warning',
                        'received quotation' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('edit_rfq')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn (Rfq $record): string => RequestForQuotation::getUrl(['rfq' => $record->id])),

                Tables\Actions\Action::make('copy_data')
                    ->label('Copy Data')
                    ->icon('heroicon-o-document-duplicate')
                    ->requiresConfirmation()
                    ->action(function (Rfq $record): void {
                        $record->loadMissing(['items', 'exchangeRates']);

                        $newRfq = Rfq::query()->create(array_merge(
                            $record->only([
                                'to_company',
                                'to_pic',
                                'rfq_date',
                                'from_company',
                                'from_department',
                                'from_pic',
                                'tel',
                                'email',
                                'model',
                                'customer',
                                'product_name',
                                'standard_qty',
                                'drawing_timing',
                                'ots_target',
                                'otop_target',
                                'sop',
                                'target_note',
                                'quotation_due_date',
                                'delivery_location',
                                'price_incoterm',
                                'tooling_payment_method',
                                'raw_material_period',
                                'material_type',
                                'material_cps_price',
                                'exchange_period',
                            ]),
                            [
                                'status' => 'draft',
                                'created_by' => auth()->id(),
                            ]
                        ));

                        foreach ($record->items as $item) {
                            $newRfq->items()->create([
                                'part_number' => $item->part_number,
                                'part_name' => $item->part_name,
                                'qty_mon' => $item->qty_mon,
                                'sort_order' => $item->sort_order,
                            ]);
                        }

                        foreach ($record->exchangeRates as $rate) {
                            $newRfq->exchangeRates()->create([
                                'currency' => $rate->currency,
                                'rate' => $rate->rate,
                                'sort_order' => $rate->sort_order,
                            ]);
                        }

                        Notification::make()
                            ->title('RFQ copied successfully')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('change_status')
                    ->label('Change Status')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'draft',
                                'emailed' => 'emailed',
                                'wait quotation' => 'wait quotation',
                                'received quotation' => 'received quotation',
                            ])
                            ->required(),
                    ])
                    ->fillForm(fn (Rfq $record): array => [
                        'status' => $record->status ?? 'draft',
                    ])
                    ->action(function (Rfq $record, array $data): void {
                        $current = $record->status ?? 'draft';
                        $next = (string) $data['status'];
                        $allowedNext = self::STATUS_FLOW[$current] ?? ['draft'];

                        if (! in_array($next, $allowedNext, true)) {
                            Notification::make()
                                ->title('Invalid status transition')
                                ->danger()
                                ->body("Allowed transition from '{$current}' to: " . implode(', ', $allowedNext))
                                ->send();

                            return;
                        }

                        $record->update(['status' => $next]);

                        Notification::make()
                            ->title('RFQ status updated')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('send_email')
                    ->label('Send Email')
                    ->icon('heroicon-o-paper-airplane')
                    ->form([
                        TextInput::make('recipient_email')
                            ->label('Recipient Email')
                            ->email()
                            ->required(),

                        Textarea::make('body_template')
                            ->label('Email Body Template')
                            ->rows(8)
                            ->required()
                            ->default("Dear {{to_company}},\n\nPlease find attached Request For Quotation (RFQ) #{{rfq_id}} for product {{product_name}}.\n\nRFQ Date: {{rfq_date}}\nQuotation Due Date: {{quotation_due_date}}\n\nBest regards,\n{{from_company}}\n{{from_department}}")
                            ->helperText('You can use placeholders: {{rfq_id}}, {{to_company}}, {{to_pic}}, {{product_name}}, {{rfq_date}}, {{quotation_due_date}}, {{from_company}}, {{from_department}}, {{from_pic}}'),
                    ])
                    ->action(function (Rfq $record, array $data): void {
                        try {
                            $record->loadMissing(['items', 'exchangeRates']);

                            Storage::disk('local')->makeDirectory('rfq_exports');
                            $timestamp = now()->format('Ymd_His');
                            $pdfRelativePath = "rfq_exports/rfq_{$record->id}_{$timestamp}.pdf";

                            $pdfAbsolutePath = storage_path('app/' . $pdfRelativePath);
                            Pdf::loadView('exports.rfq-pdf', ['rfq' => $record])->save($pdfAbsolutePath);

                            $tokens = [
                                '{{rfq_id}}' => (string) $record->id,
                                '{{to_company}}' => (string) $record->to_company,
                                '{{to_pic}}' => (string) $record->to_pic,
                                '{{product_name}}' => (string) $record->product_name,
                                '{{rfq_date}}' => optional($record->rfq_date)->format('d M Y') ?? '-',
                                '{{quotation_due_date}}' => optional($record->quotation_due_date)->format('d M Y') ?? '-',
                                '{{from_company}}' => (string) $record->from_company,
                                '{{from_department}}' => (string) $record->from_department,
                                '{{from_pic}}' => (string) $record->from_pic,
                            ];

                            $bodyText = strtr((string) $data['body_template'], $tokens);
                            $bodyHtml = nl2br(e($bodyText));

                            Mail::to($data['recipient_email'])
                                ->send(new RfqMail($record, $bodyHtml, [
                                    [
                                        'path' => $pdfAbsolutePath,
                                        'name' => "RFQ_{$record->id}.pdf",
                                        'mime' => 'application/pdf',
                                    ],
                                ]));

                            if (($record->status ?? 'draft') === 'draft') {
                                $record->update(['status' => 'emailed']);
                            }

                            Notification::make()
                                ->title('Email sent successfully with PDF attachment')
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Failed to send email')
                                ->danger()
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRfqs::route('/'),
        ];
    }
}
