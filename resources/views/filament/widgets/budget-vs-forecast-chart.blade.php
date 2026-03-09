@php
    use Filament\Support\Facades\FilamentView;
    use Filament\Support\Facades\FilamentAsset;
    
    $chartData = $this->getChartData();
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Budget vs Forecast Amount (FY {{ $this->year }})
        </x-slot>

        <div class="space-y-6">
            <div>
                {{ $this->form }}
            </div>

            <div>
                <div
                    @if (FilamentView::hasSpaMode())
                        x-load="visible"
                    @else
                        x-load
                    @endif
                    x-load-src="{{ FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                    wire:ignore
                    x-data="chart({
                        cachedData: @js($chartData['data']),
                        options: @js($chartData['options'] ?? []),
                        type: @js($chartData['type']),
                    })"
                >
                    <canvas
                        x-ref="canvas"
                        style="max-height: 400px"
                    ></canvas>

                    <span
                        x-ref="backgroundColorElement"
                        class="text-gray-100 dark:text-gray-800"
                    ></span>

                    <span
                        x-ref="borderColorElement"
                        class="text-gray-400"
                    ></span>

                    <span
                        x-ref="gridColorElement"
                        class="text-gray-200 dark:text-gray-800"
                    ></span>

                    <span
                        x-ref="textColorElement"
                        class="text-gray-500 dark:text-gray-400"
                    ></span>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>








