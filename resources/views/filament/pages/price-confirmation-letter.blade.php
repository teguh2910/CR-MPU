<x-filament-panels::page>
    <form wire:submit="print">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            {{-- The action is already defined in getHeaderActions --}}
        </div>
    </form>

    <x-filament-actions::modals />
</x-filament-panels::page>
