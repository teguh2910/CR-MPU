<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">List Price Part Number</x-slot>

        {{ $this->form }}

        <div class="mt-6">
            {{ $this->table }}
        </div>
    </x-filament::section>
</x-filament-panels::page>