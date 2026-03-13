<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Select Report Criteria</x-slot>

        <form wire:submit="saveReport">
            {{ $this->form }}

            @php
                $rows = $this->reportRows;
                $year = $this->data['year'] ?? null;
                $months = \App\Filament\Pages\ReportMonthly::months();
                $activityRows = $rows
                    ->flatMap(function ($partNumber) {
                        return $partNumber->activities->map(function ($activity) use ($partNumber) {
                            return [
                                'partNumber' => $partNumber,
                                'activity' => $activity,
                            ];
                        });
                    })
                    ->sortBy(function (array $row) {
                        return strtolower((string) ($row['activity']->cr_no ?? ''));
                    })
                    ->values();
            @endphp

            @if($rows->isNotEmpty())
                <div class="mt-6 overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
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
                                    @php
                                        $updateLabel = $this->getSelectedUpdateQtyMonthLabel($month);
                                        $isBudgetActOl = ($this->data['update_qty_month_id_' . strtolower($month)] ?? null) === 'budget';
                                    @endphp
                                    <th colspan="2" class="px-3 py-2 text-center font-semibold text-gray-700 dark:text-gray-100 whitespace-nowrap border-l border-gray-200 dark:border-gray-600">
                                        {{ substr($month, 0, 3) }}
                                        @if($updateLabel)
                                            <div class="text-[10px] font-normal {{ $isBudgetActOl ? 'text-blue-600 dark:text-blue-400' : 'text-green-600 dark:text-green-400' }} truncate max-w-[100px] mx-auto">{{ $updateLabel }}</div>
                                        @endif
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
                                            $obg   = $this->resolveActivityOBG($activity, $partNumber, $month);
                                            $actOl = $this->resolveActivityActOL($activity, $partNumber, $month);
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
                                @php $totalActivities = $activityRows->count(); @endphp
                                <td colspan="6" class="px-3 py-2 font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">
                                    Total {{ $totalActivities }} row(s) &mdash; {{ $year ?: '-' }}
                                </td>
                                @php
                                    $grandTotalObg = 0;
                                    $grandTotalActOl = 0;
                                @endphp
                                @foreach($months as $month)
                                    @php
                                        $totalObg = 0;
                                        $totalActOl = 0;
                                        foreach ($rows as $pn) {
                                            foreach ($pn->activities as $act) {
                                                $totalObg   += $this->resolveActivityOBG($act, $pn, $month);
                                                $totalActOl += $this->resolveActivityActOL($act, $pn, $month);
                                            }
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

                <div class="mt-6 flex justify-end gap-3">
                    <x-filament::button
                        type="submit"
                        icon="heroicon-o-check-circle"
                        color="success"
                        size="lg"
                    >
                        Save Report
                    </x-filament::button>
                </div>
            @elseif(($this->data['year'] ?? null))
                <div class="mt-6 py-12 text-center text-gray-400 dark:text-gray-500">
                    <x-heroicon-o-magnifying-glass class="mx-auto h-10 w-10 mb-3" />
                    <p class="text-sm">No part numbers found for the selected year.</p>
                </div>
            @endif
        </form>
    </x-filament::section>
</x-filament-panels::page>
