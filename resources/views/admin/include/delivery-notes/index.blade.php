<x-catalog.page title="Здаточні">
    @if($order_names->isNotEmpty())
        @livewire($livewire_search)
    @else
        <x-catalog.panel>Нема замовлень</x-catalog.panel>
    @endif
</x-catalog.page>
