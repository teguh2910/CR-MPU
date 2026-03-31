<div class="space-y-6 p-4">
        {{-- Part Number Info --}}
        <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Part No</div>
                <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $data['part_number']->part_no }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-300">{{ $data['part_number']->part_name }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Supplier</div>
                <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $data['part_number']->supplier?->name ?? '-' }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-300">{{ $data['part_number']->category?->name ?? '-' }}</div>
            </div>
        </div>

        {{-- Raw Material Details --}}
        @if(count($data['rm_details']) > 0)
            <div>
                <h4 class="text-sm font-semibold text-blue-700 dark:text-blue-300 mb-2">Raw Material Details</h4>
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-blue-50 dark:bg-blue-900/20">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium">Currency</th>
                                <th class="px-3 py-2 text-right font-medium">Basis Price</th>
                                <th class="px-3 py-2 text-right font-medium">Weight (g)</th>
                                <th class="px-3 py-2 text-right font-medium">Ex-Rate</th>
                                <th class="px-3 py-2 text-right font-medium">Price IDR</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($data['rm_details'] as $rm)
                                <tr>
                                    <td class="px-3 py-2">{{ $rm['currency'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-right">{{ number_format($rm['basis_price'], 2) }}</td>
                                    <td class="px-3 py-2 text-right">{{ number_format($rm['weight'], 0) }}</td>
                                    <td class="px-3 py-2 text-right">{{ $rm['ex_rate'] !== null ? number_format($rm['ex_rate'], 2) : '-' }}</td>
                                    <td class="px-3 py-2 text-right font-medium">{{ number_format($rm['price_idr'], 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-blue-50/50 dark:bg-blue-900/10">
                            <tr>
                                <td colspan="4" class="px-3 py-2 text-right font-semibold">Total RM Price IDR:</td>
                                <td class="px-3 py-2 text-right font-bold">{{ number_format($data['rm_price_idr_total'], 0) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

        {{-- Process Cost Details --}}
        @if($data['process_costs']->count() > 0)
            <div>
                <h4 class="text-sm font-semibold text-green-700 dark:text-green-300 mb-2">Process Cost Details</h4>
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-green-50 dark:bg-green-900/20">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium">Process Cost Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($data['process_costs'] as $cost)
                                <tr>
                                    <td class="px-3 py-2 text-right font-medium">{{ number_format($cost->process_cost_total, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-green-50/50 dark:bg-green-900/10">
                            <tr>
                                <td class="px-3 py-2 text-right font-bold">Total: {{ number_format($data['process_cost_total'], 0) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

        {{-- FOH & Profiy Details --}}
        @if($data['foh_profiy']->count() > 0)
            <div>
                <h4 class="text-sm font-semibold text-yellow-700 dark:text-yellow-300 mb-2">FOH & Profiy Details</h4>
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-yellow-50 dark:bg-yellow-900/20">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium">Percentage</th>
                                <th class="px-3 py-2 text-right font-medium">Amount</th>
                                <th class="px-3 py-2 text-left font-medium">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($data['foh_profiy'] as $foh)
                                <tr>
                                    <td class="px-3 py-2">{{ number_format($foh->percentage, 0) }}%</td>
                                    <td class="px-3 py-2 text-right font-medium">{{ number_format(($data['rm_price_idr_total'] * $foh->percentage) / 100, 0) }}</td>
                                    <td class="px-3 py-2">{{ $foh->remarks ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-yellow-50/50 dark:bg-yellow-900/10">
                            <tr>
                                <td class="px-3 py-2 text-right font-semibold">Total FOH & Profiy:</td>
                                <td class="px-3 py-2 text-right font-bold">{{ number_format($data['foh_profiy_amount'], 0) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

        {{-- Other Cost Details --}}
        @if($data['other_costs']->count() > 0)
            <div>
                <h4 class="text-sm font-semibold text-purple-700 dark:text-purple-300 mb-2">Other Cost Details</h4>
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-purple-50 dark:bg-purple-900/20">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium">Remark</th>
                                <th class="px-3 py-2 text-right font-medium">Cost</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($data['other_costs'] as $cost)
                                <tr>
                                    <td class="px-3 py-2">{{ $cost->remark ?? '-' }}</td>
                                    <td class="px-3 py-2 text-right font-medium">{{ number_format($cost->cost, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-purple-50/50 dark:bg-purple-900/10">
                            <tr>
                                <td class="px-3 py-2 text-right font-semibold">Total Other Cost:</td>
                                <td class="px-3 py-2 text-right font-bold">{{ number_format($data['other_cost_total'], 0) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

        {{-- Tooling Cost Details --}}
        @if($data['tooling_costs']->count() > 0)
            <div>
                <h4 class="text-sm font-semibold text-orange-700 dark:text-orange-300 mb-2">Tooling Cost Details</h4>
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-orange-50 dark:bg-orange-900/20">
                            <tr>
                                <th class="px-3 py-2 text-right font-medium">Tooling Price</th>
                                <th class="px-3 py-2 text-right font-medium">Depre/Pcs</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($data['tooling_costs'] as $tooling)
                                <tr>
                                    <td class="px-3 py-2 text-right">{{ number_format($tooling->tooling_price, 0) }}</td>
                                    <td class="px-3 py-2 text-right">{{ number_format($tooling->depre_per_pcs, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-orange-50/50 dark:bg-orange-900/10">
                            <tr>
                                <td class="px-3 py-2 text-right font-semibold">Total Tooling Cost:</td>
                                <td class="px-3 py-2 text-right font-bold">{{ number_format($data['tooling_cost_total'], 0) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

        {{-- Grand Total --}}
        <div class="mt-6 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-700">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-sm text-red-700 dark:text-red-300">Grand Total</div>
                    <div class="text-xs text-red-600 dark:text-red-400 mt-1">
                        RM Price + Process Cost + FOH & Profiy + Other Cost + Tooling Cost
                    </div>
                </div>
                <div class="text-2xl font-bold text-red-900 dark:text-red-200">
                    {{ number_format($data['grand_total'], 0) }}
                </div>
            </div>
        </div>
    </div>
</div>