<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Request For Quotation Form</x-slot>

        <form wire:submit="save" class="space-y-4">
            {{ $this->form }}

            <div class="flex justify-end">
                <x-filament::button type="submit" icon="heroicon-o-check-circle">
                    Save RFQ
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>

    @php
        $partItems = collect($this->data['part_items'] ?? []);
        $exchangeRates = collect($this->data['exchange_rates'] ?? []);
    @endphp

    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <style>
            .rfq-wrap {
                background-color: #fff;
                max-width: 980px;
                margin: 0 auto;
                padding: 28px;
                border: 1px solid #111;
                color: #111;
                font-family: Arial, sans-serif;
                font-size: 13px;
            }
            .rfq-hi {
                background: #fff9bf;
            }
            .rfq-table,
            .rfq-table th,
            .rfq-table td {
                border: 1px solid #111;
                border-collapse: collapse;
            }
            .rfq-table th,
            .rfq-table td {
                padding: 4px 7px;
                vertical-align: middle;
            }
            .rfq-section {
                margin-top: 14px;
                margin-bottom: 4px;
                font-weight: bold;
            }
            .rfq-title {
                text-align: center;
                font-size: 34px;
                font-weight: 800;
                margin: 18px 0;
                text-transform: lowercase;
            }
        </style>

        <div class="rfq-wrap">
            <div style="display:flex;justify-content:space-between;gap:16px;">
                <div style="flex:1;">
                    <div style="font-size:24px;font-weight:bold;">To: <span style="font-weight:400;">{{ $this->data['to_company'] ?? '-' }}</span></div>
                    <div class="rfq-hi" style="display:inline-block;margin-top:4px;padding:6px 12px;font-weight:700;">{{ $this->data['to_pic'] ?? '-' }}</div>
                </div>
                <div style="flex:1;text-align:right;line-height:1.35;">
                    <div class="rfq-hi" style="display:inline-block;padding:3px 8px;">{{ filled($this->data['rfq_date'] ?? null) ? \Illuminate\Support\Carbon::parse($this->data['rfq_date'])->format('d-M-Y') : '-' }}</div>
                    <div><strong>{{ $this->data['from_company'] ?? '-' }}</strong></div>
                    <div>{{ $this->data['from_department'] ?? '-' }}</div>
                    <div class="rfq-hi" style="display:inline-block;padding:3px 8px;margin-top:2px;">PIC: {{ $this->data['from_pic'] ?? '-' }}</div>
                    <div style="margin-top:8px;">TEL: {{ $this->data['tel'] ?? '-' }}</div>
                    <div>E-mail: <span style="text-decoration:underline;color:#0d6efd;">{{ $this->data['email'] ?? '-' }}</span></div>
                </div>
            </div>

            <div class="rfq-title">Request for quotation</div>

            <div class="rfq-section">[Product information]</div>
            <table class="rfq-table" style="width:100%;text-align:center;">
                <thead>
                    <tr>
                        <th style="width:20%;">Model</th>
                        <th style="width:20%;">Customer</th>
                        <th style="width:35%;">product name</th>
                        <th style="width:25%;">Standard Qty</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="rfq-hi">{{ $this->data['model'] ?? '-' }}</td>
                        <td>{{ $this->data['customer'] ?? '-' }}</td>
                        <td class="rfq-hi">{{ $this->data['product_name'] ?? '-' }}</td>
                        <td><span class="rfq-hi" style="padding:1px 5px;">{{ $this->data['standard_qty'] ?? '-' }}</span> /Mon</td>
                    </tr>
                </tbody>
            </table>

            <table class="rfq-table" style="width:100%;text-align:center;border-top:0;">
                <thead>
                    <tr>
                        <th style="width:25%;">Drawing timing</th>
                        <th style="width:25%;">OTS target</th>
                        <th style="width:25%;">OTOP target</th>
                        <th style="width:25%;">SOP</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="rfq-hi">{{ $this->data['drawing_timing'] ?? '-' }}</td>
                        <td>{{ filled($this->data['ots_target'] ?? null) ? \Illuminate\Support\Carbon::parse($this->data['ots_target'])->format('d-M-y') : '-' }}</td>
                        <td>{{ filled($this->data['otop_target'] ?? null) ? \Illuminate\Support\Carbon::parse($this->data['otop_target'])->format('d-M-y') : '-' }}</td>
                        <td class="rfq-hi">{{ $this->data['sop'] ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="rfq-section">[Target of request for quotation]</div>
            <table class="rfq-table" style="width:100%;">
                <thead style="text-align:center;">
                    <tr>
                        <th style="width:50px;">No</th>
                        <th style="width:190px;">Parts Number</th>
                        <th>Parts Name</th>
                        <th style="width:100px;">Qty/Mon</th>
                        <th style="width:280px;">Note</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($partItems as $part)
                        <tr>
                            <td style="text-align:center;">{{ $loop->iteration }}</td>
                            <td>{{ $part['part_number'] ?? '-' }}</td>
                            <td class="rfq-hi">{{ $part['part_name'] ?? '-' }}</td>
                            <td class="rfq-hi" style="text-align:center;">{{ $part['qty_mon'] ?? '-' }}</td>
                            @if($loop->first)
                                <td class="rfq-hi" rowspan="{{ max($partItems->count(), 1) }}" style="vertical-align:top;white-space:pre-line;font-size:12px;">{{ $this->data['target_note'] ?? '-' }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td style="text-align:center;">1</td>
                            <td>-</td>
                            <td class="rfq-hi">-</td>
                            <td class="rfq-hi" style="text-align:center;">-</td>
                            <td class="rfq-hi" style="white-space:pre-line;font-size:12px;">{{ $this->data['target_note'] ?? '-' }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="rfq-section">[Quotation Due date]</div>
            <table class="rfq-table" style="width:280px;text-align:center;">
                <tr>
                    <td class="rfq-hi" style="padding:7px 8px;">{{ filled($this->data['quotation_due_date'] ?? null) ? \Illuminate\Support\Carbon::parse($this->data['quotation_due_date'])->format('d-M-y') : '-' }}</td>
                </tr>
            </table>

            <div class="rfq-section">[Quotation prerequisites]</div>
            <table class="rfq-table" style="width:100%;">
                <tr>
                    <td style="width:35%;">Delivery location:</td>
                    <td class="rfq-hi">{{ $this->data['delivery_location'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Price incoterm:</td>
                    <td class="rfq-hi">{{ $this->data['price_incoterm'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Tooling payment method:</td>
                    <td class="rfq-hi">{{ $this->data['tooling_payment_method'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td>raw material period:</td>
                    <td class="rfq-hi">{{ $this->data['raw_material_period'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Material AISIN CPS or Non-CPS:</td>
                    <td class="rfq-hi">{{ $this->data['material_type'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Material AISIN CPS Price:</td>
                    <td class="rfq-hi">{{ $this->data['material_cps_price'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td>exchange rate(period):</td>
                    <td class="rfq-hi" style="padding:0;">
                        <div style="padding:6px 8px;">{{ $this->data['exchange_period'] ?? '-' }}</div>
                        <table style="width:100%;border-top:1px solid #111;border-collapse:collapse;">
                            <tbody>
                                @foreach($exchangeRates->chunk(2) as $chunk)
                                    <tr>
                                        @foreach($chunk as $rate)
                                            <td style="padding:6px 8px;">1{{ strtoupper($rate['currency'] ?? '-') }} =</td>
                                            <td style="padding:6px 8px;text-align:center;border-left:1px solid #111;border-right:1px solid #111;background:#fff;">{{ $rate['rate'] ?? '-' }}</td>
                                            <td style="padding:6px 8px;">IDR</td>
                                        @endforeach
                                        @if($chunk->count() < 2)
                                            <td></td><td></td><td></td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>

            <div style="margin-top:16px;font-size:15px;">If you have any inquiries, please contact PIC</div>
        </div>
    </div>
</x-filament-panels::page>
