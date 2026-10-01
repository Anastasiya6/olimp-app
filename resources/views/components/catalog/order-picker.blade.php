@props(['orders', 'selected' => [], 'id'])
@php
    $selectedIds = array_values(array_unique(array_map('strval', (array) $selected)));
    $options = $orders->map(fn ($order) => ['id' => (string) $order->id, 'name' => (string) $order->name])->values();
@endphp
<div x-data="{ selected: @js($selectedIds), options: @js($options), search: '', open: false, matches(name) { return name.toLocaleLowerCase().includes(this.search.trim().toLocaleLowerCase()); } }"
    x-on:click.outside="open = false"
    x-on:focusout="if (!$el.contains($event.relatedTarget)) open = false"
    x-on:keydown.escape="if (open) { open = false; $event.stopPropagation(); }">
    <label for="{{ $id }}-search" class="catalog-label">Замовлення</label>
    <div class="overflow-hidden rounded-md border border-[#a8c8c5] bg-[#f7fbfa] focus-within:border-teal-600 focus-within:ring-1 focus-within:ring-teal-600">
    <div class="flex min-h-[48px] cursor-text items-center gap-2 px-3 py-2" x-on:click="open = true; $refs.search.focus()">
    <div class="flex min-w-0 flex-1 flex-wrap items-center gap-2">
        <template x-for="orderId in selected" :key="orderId">
            <span class="inline-flex max-w-full items-center gap-1 rounded-md border border-[#a8c8c5] bg-[#edf6f3] pl-3 text-base font-semibold text-[#245b53]">
                <span class="break-words" x-text="options.find(order => order.id === orderId)?.name || orderId"></span>
                <button type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md hover:bg-[#cee6e2] focus:outline-none focus:ring-2 focus:ring-teal-600"
                    :aria-label="'Прибрати замовлення ' + (options.find(order => order.id === orderId)?.name || orderId)"
                    x-on:click.stop="selected = selected.filter(value => value !== orderId)">
                    <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m6 6 12 12M6 18 18 6" /></svg>
                </button>
            </span>
        </template>
        <input id="{{ $id }}-search" x-ref="search" type="search" x-model="search"
            x-on:focus="open = true" x-on:input="open = true" x-on:keydown.arrow-down.prevent="open = true"
            x-on:keydown.enter.prevent="open = true"
            :placeholder="selected.length ? 'Додати…' : 'Оберіть замовлення…'" autocomplete="off"
            aria-controls="{{ $id }}-options" :aria-expanded="open" class="order-picker-search min-w-[8rem] flex-1" />
    </div>
        <button type="button" x-on:click.stop="open = !open; if (open) $refs.search.focus()" :aria-expanded="open" aria-controls="{{ $id }}-options" aria-label="Показати або сховати замовлення"
            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md text-[#245b53] hover:bg-[#edf6f3] focus:outline-none focus:ring-2 focus:ring-teal-600">
            <svg aria-hidden="true" class="h-5 w-5 transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
        </button>
    </div>
    <div id="{{ $id }}-options" x-show="open" style="display: none" class="max-h-48 overflow-y-auto border-t border-[#a8c8c5] bg-white" role="group" aria-label="Вибір замовлень">
        @foreach($orders as $order)
            <label class="flex cursor-pointer items-center gap-3 border-b border-[#d4e5e0] px-3 py-2 last:border-b-0 hover:bg-[#edf6f3]"
                data-order-name="{{ $order->name }}" x-show="matches($el.dataset.orderName)">
                <input type="checkbox" name="orders[]" value="{{ $order->id }}" @checked(in_array((string) $order->id, $selectedIds, true)) x-model="selected"
                    class="h-5 w-5 shrink-0 rounded border-[#a8c8c5] text-teal-700 focus:ring-teal-600" />
                <span class="break-words">{{ $order->name }}</span>
            </label>
        @endforeach
        <p x-show="!options.some(order => matches(order.name))" class="px-3 py-3 text-sm text-slate-500">Замовлень не знайдено.</p>
    </div>
    </div>
</div>
