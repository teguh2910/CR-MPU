<x-filament-panels::page>
    @php
        $months = $this->months();
        $activityRows = $this->activityRows;
    @endphp

    <x-filament::section>
        <x-slot name="heading">Monthly Report History Detail</x-slot>

        <div class="text-sm text-gray-600 dark:text-gray-300">
            <span class="font-semibold">Remarks:</span> {{ $this->remarks ?: '-' }}
            <span class="mx-2">|</span>
            <span class="font-semibold">Year:</span> {{ $this->year ?: '-' }}
        </div>

        <div class="mt-4 flex justify-end">
            <x-filament::button
                tag="a"
                href="{{ route('report-monthly-transactions.export', ['year' => $this->year, 'remarksKey' => \App\Filament\Resources\ReportMonthlyTransactionResource::encodeRemarksKey($this->remarks)]) }}"
                icon="heroicon-o-arrow-down-tray"
                color="success"
            >
                Export Excel
            </x-filament::button>
        </div>

        @if($activityRows->isNotEmpty())
            <div class="mt-4 overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th rowspan="2" class="px-3 py-2 text-left font-semibold text-gray-700 dark:text-gray-100 whitespace-nowrap border-r border-gray-200 dark:border-gray-600 align-middle">CR NO.</th>
                            <th rowspan="2" class="px-3 py-2 text-center font-semibold text-gray-700 dark:text-gray-100 whitespace-nowrap border-r border-gray-200 dark:border-gray-600 align-middle">No</th>
                            <th rowspan="2" class="px-3 py-2 text-left font-semibold text-gray-700 dark:text-gray-100 whitespace-nowrap border-r border-gray-200 dark:border-gray-600 align-middle">CR Activity</th>
                            <th rowspan="2" class="px-3 py-2 text-left font-semibold text-gray-700 dark:text-gray-100 whitespace-nowrap border-r border-gray-200 dark:border-gray-600 align-middle">Item CR</th>
                            <th rowspan="2" class="px-3 py-2 text-left font-semibold text-gray-700 dark:text-gray-100 whitespace-nowrap border-r border-gray-200 dark:border-gray-600 align-middle">Category Product</th>
                            <th rowspan="2" class="px-3 py-2 text-left font-semibold text-gray-700 dark:text-gray-100 whitespace-nowrap border-r border-gray-200 dark:border-gray-600 align-middle">CRP Expense</th>
                            @foreach($months as $month)
                                <th colspan="2" class="px-3 py-2 text-center font-semibold text-gray-700 dark:text-gray-100 whitespace-nowrap border-l border-gray-200 dark:border-gray-600">
                                    {{ substr($month, 0, 3) }}
                                </th>
                            @endforeach
                            <th rowspan="2" class="px-3 py-2 text-right font-semibold text-blue-600 dark:text-blue-400 whitespace-nowrap border-l border-gray-200 dark:border-gray-600 align-middle">Total Amount OBG</th>
                            <th rowspan="2" class="px-3 py-2 text-right font-semibold text-green-600 dark:text-green-400 whitespace-nowrap border-l border-gray-200 dark:border-gray-600 align-middle">Total Amount Act/OL</th>
                        </tr>
                        <tr>
                            @foreach($months as $month)
                                <th class="px-2 py-1 text-center font-semibold text-blue-600 dark:text-blue-400 whitespace-nowrap border-l border-gray-200 dark:border-gray-600">OBG</th>
                                <th class="px-2 py-1 text-center font-semibold text-green-600 dark:text-green-400 whitespace-nowrap">Act/OL</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-900">
                        @php $rowNo = 1; @endphp
                        @foreach($activityRows as $row)
                            @php
                                $partNumber = $row['partNumber'];
                                $activity = $row['activity'];
                                $txByMonth = $row['txByMonth'];
                                $rowTotalObg = 0;
                                $rowTotalActOl = 0;
                            @endphp
                            <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-3 py-2 font-mono text-gray-700 dark:text-gray-200 whitespace-nowrap border-r border-gray-100 dark:border-gray-700">{{ $activity->cr_no ?? '-' }}</td>
                                <td class="px-3 py-2 text-center text-gray-500 dark:text-gray-400 border-r border-gray-100 dark:border-gray-700">{{ $rowNo++ }}</td>
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-200 border-r border-gray-100 dark:border-gray-700">{{ $activity->activity ?? '-' }}</td>
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-200 whitespace-nowrap border-r border-gray-100 dark:border-gray-700">
                                    <div class="font-mono font-medium">{{ $partNumber->part_no }}</div>
                                    <div class="text-gray-500 dark:text-gray-400">{{ $partNumber->part_name }}</div>
                                </td>
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-200 whitespace-nowrap border-r border-gray-100 dark:border-gray-700">{{ $partNumber->product?->name ?? '-' }}</td>
                                <td class="px-3 py-2 text-gray-700 dark:text-gray-200 whitespace-nowrap border-r border-gray-100 dark:border-gray-700">{{ $partNumber->category?->name ?? '-' }}</td>
                                @foreach($months as $month)
                                    @php
                                        $obg   = $this->resolveActivityOBG($activity, $txByMonth, $month);
                                        $actOl = $this->resolveActivityActOL($activity, $txByMonth, $month);
                                        $rowTotalObg += $obg;
                                        $rowTotalActOl += $actOl;
                                    @endphp
                                    <td class="px-2 py-2 text-right border-l border-gray-100 dark:border-gray-700 {{ $obg > 0 ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-400' }}">
                                        {{ $obg > 0 ? number_format($obg) : '-' }}
                                    </td>
                                    <td class="px-2 py-2 text-right {{ $actOl > 0 ? 'text-green-600 dark:text-green-400 font-medium' : 'text-gray-400' }}">
                                        {{ $actOl > 0 ? number_format($actOl) : '-' }}
                                    </td>
                                @endforeach
                                <td class="px-3 py-2 text-right font-semibold text-blue-600 dark:text-blue-400 border-l border-gray-200 dark:border-gray-600 whitespace-nowrap">
                                    {{ number_format($rowTotalObg) }}
                                </td>
                                <td class="px-3 py-2 text-right font-semibold text-green-600 dark:text-green-400 border-l border-gray-200 dark:border-gray-600 whitespace-nowrap">
                                    {{ number_format($rowTotalActOl) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-800 border-t-2 border-gray-200 dark:border-gray-700">
                        <tr>
                            @php
                                $totalActivities = $activityRows->count();
                                $grandTotalObg = 0;
                                $grandTotalActOl = 0;
                            @endphp
                            <td colspan="6" class="px-3 py-2 font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">
                                Total {{ $totalActivities }} row(s) - {{ $this->year ?: '-' }}
                            </td>
                            @foreach($months as $month)
                                @php
                                    $totalObg = 0;
                                    $totalActOl = 0;
                                    foreach ($activityRows as $row) {
                                        $totalObg += $this->resolveActivityOBG($row['activity'], $row['txByMonth'], $month);
                                        $totalActOl += $this->resolveActivityActOL($row['activity'], $row['txByMonth'], $month);
                                    }
                                    $grandTotalObg += $totalObg;
                                    $grandTotalActOl += $totalActOl;
                                @endphp
                                <td class="px-2 py-2 text-right font-semibold text-blue-600 dark:text-blue-400 border-l border-gray-200 dark:border-gray-600 whitespace-nowrap">
                                    {{ number_format($totalObg) }}
                                </td>
                                <td class="px-2 py-2 text-right font-semibold text-green-600 dark:text-green-400 whitespace-nowrap">
                                    {{ number_format($totalActOl) }}
                                </td>
                            @endforeach
                            <td class="px-3 py-2 text-right font-bold text-blue-700 dark:text-blue-300 border-l border-gray-200 dark:border-gray-600 whitespace-nowrap">
                                {{ number_format($grandTotalObg) }}
                            </td>
                            <td class="px-3 py-2 text-right font-bold text-green-700 dark:text-green-300 border-l border-gray-200 dark:border-gray-600 whitespace-nowrap">
                                {{ number_format($grandTotalActOl) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="mt-4 py-10 text-center text-gray-400 dark:text-gray-500">
                <p class="text-sm">No report snapshot rows found for this remarks and year.</p>
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
