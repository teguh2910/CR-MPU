<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Check Quotation</x-slot>

        {{ $this->form }}

        @php
            $result = $this->quotationResult;
        @endphp

        @if($result)
            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Part No</div>
                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $result['part_number']->part_no }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">{{ $result['part_number']->part_name }}</div>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Period</div>
                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ \Illuminate\Support\Carbon::parse($result['period_from'])->format('d M Y') }} - {{ \Illuminate\Support\Carbon::parse($result['period_to'])->format('d M Y') }}</div>
                </div>
            </div>

            <div class="mt-6 overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-gray-700 dark:text-gray-200">RM Period</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-700 dark:text-gray-200">RM Currency</th>
                            <th class="px-3 py-2 text-right font-semibold text-gray-700 dark:text-gray-200">RM Basis Price</th>
                            <th class="px-3 py-2 text-right font-semibold text-gray-700 dark:text-gray-200">RM Weight (Gram)</th>
                            <th class="px-3 py-2 text-right font-semibold text-gray-700 dark:text-gray-200">Ex-Rate</th>
                            <th class="px-3 py-2 text-right font-semibold text-gray-700 dark:text-gray-200">RM Price IDR</th>
                            <th class="px-3 py-2 text-right font-semibold text-gray-700 dark:text-gray-200">Tooling Cost</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-900">
                        @forelse($result['rm_rows'] as $row)
                            <tr>
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-200">{{ optional($row['period_from'])->format('d M Y') }} - {{ optional($row['period_to'])->format('d M Y') }}</td>
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-200">{{ $row['rm_currency'] ?: '-' }}</td>
                                <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-200">{{ number_format($row['rm_basis_price'], 2) }}</td>
                                <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-200">{{ number_format($row['rm_weight_gram'], 0) }}</td>
                                <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-200">{{ $row['ex_rate'] !== null ? number_format($row['ex_rate'], 2) : '-' }}</td>
                                <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-200">{{ number_format($row['rm_price_idr'], 0) }}</td>
                                <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-200">{{ number_format($result['tooling_cost_total'], 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-4 text-center text-gray-400">No RM Supplier / Ex-Rate data found for selected period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3 xl:grid-cols-6">
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400">RM Price IDR Total</div>
                    <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($result['rm_price_idr_total'], 0) }}</div>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Process Cost Supplier</div>
                    <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($result['process_cost_total'], 0) }}</div>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400">FOH & Profiy Total (%)</div>
                    <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($result['foh_percentage_total'], 0) }}%</div>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400">FOH & Profiy Amount</div>
                    <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($result['foh_profiy_amount'], 0) }}</div>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Other Cost Supplier Total</div>
                    <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($result['other_cost_total'], 0) }}</div>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Tooling Cost Supplier Total</div>
                    <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($result['tooling_cost_total'], 0) }}</div>
                </div>
                <div class="rounded-lg border border-blue-300 dark:border-blue-700 bg-blue-50/50 dark:bg-blue-900/20 p-4 md:col-span-3 xl:col-span-6">
                    <div class="text-xs text-blue-700 dark:text-blue-300">Total</div>
                    <div class="mt-1 text-2xl font-bold text-blue-900 dark:text-blue-200">{{ number_format($result['grand_total'], 0) }}</div>
                    <div class="mt-1 text-xs text-blue-700 dark:text-blue-300">
                        Formula: RM Price IDR + Process Cost Supplier + FOH &amp; Profiy Supplier + Other Cost Supplier + Tooling Cost Supplier
                    </div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700 dark:text-gray-200">Process Cost Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-900">
                            @forelse($result['process_costs'] as $row)
                                <tr>
                                    <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-200">{{ number_format((float) $row->process_cost_total, 0) }}</td>
                                </tr>
                            @empty
                                <tr><td class="px-3 py-4 text-center text-gray-400">No data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700 dark:text-gray-200">FOH & Profiy (%)</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-700 dark:text-gray-200">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-900">
                            @forelse($result['foh_profiy'] as $row)
                                <tr>
                                    <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-200">{{ number_format((float) $row->percentage, 0) }}%</td>
                                    <td class="px-3 py-2 text-gray-700 dark:text-gray-200">{{ $row->remarks ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="px-3 py-4 text-center text-gray-400">No data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-gray-700 dark:text-gray-200">Other Cost Remark</th>
                            <th class="px-3 py-2 text-right font-semibold text-gray-700 dark:text-gray-200">Cost</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-900">
                        @forelse($result['other_costs'] as $row)
                            <tr>
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-200">{{ $row->remark }}</td>
                                <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-200">{{ number_format((float) $row->cost, 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-3 py-4 text-center text-gray-400">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div class="mt-6 py-8 text-center text-gray-400 dark:text-gray-500">
                Select Part No and Period to check quotation data.
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
