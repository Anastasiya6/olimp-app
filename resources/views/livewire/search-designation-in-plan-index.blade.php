<div>
    <x-slot name="header" compact="true">
        <h2 class="text-xl font-bold leading-tight text-[#174a47]">Пошук деталі в плані</h2>
    </x-slot>
    <div class="bg-[#f2f7f6] py-3 sm:py-4">
        <div class="mx-auto max-w-7xl px-3 sm:px-6 lg:px-8">
            <x-catalog.panel class="mb-4">
                <form wire:submit="search">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <div class="w-full sm:w-56">
                            <label for="plan-designation-search" class="sr-only">Позначення деталі або вузла</label>
                            <input id="plan-designation-search" type="text" wire:model="designation_number"
                                class="compact-search" placeholder="Позначення деталі або вузла" />
                        </div>
                        <div class="w-full sm:w-48">
                            <label for="plan-order-search" class="sr-only">Замовлення</label>
                            <select id="plan-order-search" wire:model="selectedOrder" aria-label="Замовлення"
                                class="h-9 w-full rounded-md border-slate-300 py-1.5 text-sm focus:border-teal-600 focus:ring-teal-600">
                                @foreach($order_names as $order_name)
                                    <option value="{{ $order_name->id }}">{{ $order_name->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <x-catalog.button type="submit" variant="primary" class="catalog-add-button self-start">Шукати</x-catalog.button>
                    </div>
                </form>
            </x-catalog.panel>
            <div class="overflow-hidden rounded-lg border border-[#a8c8c5] bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="catalog-table">
                        <thead>
                            <tr><th scope="col">Деталь або вузол в плані</th></tr>
                        </thead>
                        <tbody>
                            @forelse($results as $item)
                                <tr><td>{{ $item->designation->designation }}</td></tr>
                            @empty
                                <tr><td class="py-8 text-center text-base font-normal text-slate-600">Нічого не знайдено</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
