@props(['items', 'title' => 'Перелік матеріалів'])
<div class="overflow-hidden rounded-lg border border-[#a8c8c5] bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="catalog-table">
            <thead>{{ $head }}</thead>
            <tbody>{{ $slot }}</tbody>
        </table>
    </div>
    <div class="border-t border-[#a8c8c5] bg-white px-4 py-4">
        <p class="text-sm text-slate-600">Записів: {{ $items instanceof \Illuminate\Contracts\Pagination\Paginator ? $items->total() : $items->count() }}</p>
        @if($items instanceof \Illuminate\Contracts\Pagination\Paginator && $items->hasPages())
            <div class="mt-3">{{ $items->appends(request()->input())->links('livewire.pagination.material-stocks') }}</div>
        @endif
    </div>
</div>
