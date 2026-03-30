<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request For Quotation #{{ $rfq->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #111;
        }
        .rfq-wrap {
            background-color: #fff;
            width: 100%;
            padding: 16px;
            border: 1px solid #111;
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
            margin-top: 12px;
            margin-bottom: 4px;
            font-weight: bold;
        }
        .rfq-title {
            text-align: center;
            font-size: 28px;
            font-weight: 800;
            margin: 14px 0;
            text-transform: lowercase;
        }
        .muted {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="rfq-wrap">
        <table style="width: 100%; border: 0;">
            <tr>
                <td style="width: 50%; border: 0; vertical-align: top;">
                    <div style="font-size: 20px; font-weight: bold;">To: <span style="font-weight: 400;">{{ $rfq->to_company }}</span></div>
                    <div class="rfq-hi" style="display: inline-block; margin-top: 4px; padding: 5px 10px; font-weight: 700;">{{ $rfq->to_pic }}</div>
                </td>
                <td style="width: 50%; border: 0; text-align: right; vertical-align: top; line-height: 1.3;">
                    <div class="rfq-hi" style="display: inline-block; padding: 2px 6px;">{{ optional($rfq->rfq_date)->format('d-M-Y') ?? '-' }}</div>
                    <div><strong>{{ $rfq->from_company }}</strong></div>
                    <div>{{ $rfq->from_department }}</div>
                    <div class="rfq-hi" style="display: inline-block; padding: 2px 6px; margin-top: 2px;">PIC: {{ $rfq->from_pic }}</div>
                    <div style="margin-top: 6px;">TEL: {{ $rfq->tel ?: '-' }}</div>
                    <div>E-mail: {{ $rfq->email ?: '-' }}</div>
                </td>
            </tr>
        </table>

        <div class="rfq-title">Request for quotation</div>

        <div class="rfq-section">[Product information]</div>
        <table class="rfq-table" style="width: 100%; text-align: center;">
            <thead>
                <tr>
                    <th style="width: 20%;">Model</th>
                    <th style="width: 20%;">Customer</th>
                    <th style="width: 35%;">product name</th>
                    <th style="width: 25%;">Standard Qty</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="rfq-hi">{{ $rfq->model ?: '-' }}</td>
                    <td>{{ $rfq->customer ?: '-' }}</td>
                    <td class="rfq-hi">{{ $rfq->product_name ?: '-' }}</td>
                    <td><span class="rfq-hi" style="padding: 1px 5px;">{{ $rfq->standard_qty ?: '-' }}</span> /Mon</td>
                </tr>
            </tbody>
        </table>

        <table class="rfq-table" style="width: 100%; text-align: center; border-top: 0;">
            <thead>
                <tr>
                    <th style="width: 25%;">Drawing timing</th>
                    <th style="width: 25%;">OTS target</th>
                    <th style="width: 25%;">OTOP target</th>
                    <th style="width: 25%;">SOP</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="rfq-hi">{{ $rfq->drawing_timing ?: '-' }}</td>
                    <td>{{ optional($rfq->ots_target)->format('d-M-y') ?: '-' }}</td>
                    <td>{{ optional($rfq->otop_target)->format('d-M-y') ?: '-' }}</td>
                    <td class="rfq-hi">{{ $rfq->sop ?: '-' }}</td>
                </tr>
            </tbody>
        </table>

        <div class="rfq-section">[Target of request for quotation]</div>
        <table class="rfq-table" style="width: 100%;">
            <thead style="text-align: center;">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 190px;">Parts Number</th>
                    <th>Parts Name</th>
                    <th style="width: 100px;">Qty/Mon</th>
                    <th style="width: 280px;">Note</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rfq->items as $item)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td>{{ $item->part_number }}</td>
                        <td class="rfq-hi">{{ $item->part_name }}</td>
                        <td class="rfq-hi" style="text-align: center;">{{ $item->qty_mon }}</td>
                        @if($loop->first)
                            <td class="rfq-hi" rowspan="{{ max($rfq->items->count(), 1) }}" style="vertical-align: top; white-space: pre-line; font-size: 11px;">{{ $rfq->target_note ?: '-' }}</td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td style="text-align: center;">1</td>
                        <td>-</td>
                        <td class="rfq-hi">-</td>
                        <td class="rfq-hi" style="text-align: center;">-</td>
                        <td class="rfq-hi" style="white-space: pre-line; font-size: 11px;">{{ $rfq->target_note ?: '-' }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="rfq-section">[Quotation Due date]</div>
        <table class="rfq-table" style="width: 280px; text-align: center;">
            <tr>
                <td class="rfq-hi" style="padding: 7px 8px;">{{ optional($rfq->quotation_due_date)->format('d-M-y') ?: '-' }}</td>
            </tr>
        </table>

        <div class="rfq-section">[Quotation prerequisites]</div>
        <table class="rfq-table" style="width: 100%;">
            <tr>
                <td style="width: 35%;">Delivery location:</td>
                <td class="rfq-hi">{{ $rfq->delivery_location ?: '-' }}</td>
            </tr>
            <tr>
                <td>Price incoterm:</td>
                <td class="rfq-hi">{{ $rfq->price_incoterm ?: '-' }}</td>
            </tr>
            <tr>
                <td>Tooling payment method:</td>
                <td class="rfq-hi">{{ $rfq->tooling_payment_method ?: '-' }}</td>
            </tr>
            <tr>
                <td>raw material period:</td>
                <td class="rfq-hi">{{ $rfq->raw_material_period ?: '-' }}</td>
            </tr>
            <tr>
                <td>Material AISIN CPS or Non-CPS:</td>
                <td class="rfq-hi">{{ $rfq->material_type ?: '-' }}</td>
            </tr>
            <tr>
                <td>Material AISIN CPS Price:</td>
                <td class="rfq-hi">{{ $rfq->material_cps_price ?: '-' }}</td>
            </tr>
            <tr>
                <td>exchange rate(period):</td>
                <td class="rfq-hi">{{ $rfq->exchange_period ?: '-' }}</td>
            </tr>
        </table>

        @if($rfq->exchangeRates->isNotEmpty())
            <table class="rfq-table" style="width: 100%; margin-top: 4px;">
                <thead>
                    <tr>
                        <th style="width: 30%;">Currency</th>
                        <th>Rate</th>
                        <th style="width: 20%;">Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rfq->exchangeRates as $rate)
                        <tr>
                            <td>1{{ strtoupper($rate->currency) }}</td>
                            <td>{{ $rate->rate }}</td>
                            <td>IDR</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <p style="margin-top: 14px;">If you have any inquiries, please contact PIC</p>
        <p class="muted">Generated by CR-MPU RFQ system.</p>
    </div>
</body>
</html>
