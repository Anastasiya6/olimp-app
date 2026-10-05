<div class="issuance-materials-page" x-data="{}">

    <x-slot name="header" compact="true">
        <h2 class="text-xl font-bold leading-tight text-[#174a47]">
            Видача матеріалів без норм
        </h2>
    </x-slot>
    <div class="bg-[#f2f7f6] py-3 sm:py-4">
        <div class="mx-auto max-w-7xl px-3 sm:px-6 lg:px-8">

                <x-catalog.panel class="mb-4">
                    <div class="flex flex-wrap justify-end gap-3">
                        <x-catalog.button class="catalog-reports-button" x-on:click="$dispatch('open-modal', 'manual-issuance-reports')" aria-haspopup="dialog">
                            <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Zm0 0v6h6M8 17v-3m4 3v-6m4 6v-2" /></svg>
                            Звіти
                        </x-catalog.button>
                        <x-catalog.button variant="primary" class="catalog-add-button" x-on:click="$dispatch('open-modal', 'create-manual-issuance')" aria-haspopup="dialog">
                            <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
                            Додати документ
                        </x-catalog.button>
                    </div>
                </x-catalog.panel>
                <x-modal name="manual-issuance-reports" maxWidth="2xl" focusable>
                    <div role="dialog" aria-modal="true" aria-labelledby="manual-issuance-reports-title">
                        <div class="flex items-center justify-between gap-3 border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4">
                            <h3 id="manual-issuance-reports-title" class="text-xl font-bold text-[#174a47]">Звіт по замовленню</h3>
                            <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити" class="rounded-md p-2 text-[#245b53] hover:bg-white">
                                <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div class="space-y-4 p-5">
                            <label class="block">
                                <span class="font-medium text-[#245b53]">Замовлення</span>
                                <select wire:model.live="reportOrderId" class="mt-1 block w-full rounded-md border-slate-300 focus:border-teal-600 focus:ring-teal-600">
                                    <option value="">Оберіть замовлення</option>
                                    @foreach($order_names as $order)
                                        <option value="{{ $order->id }}">{{ $order->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <div class="flex justify-end">
                                @if($reportOrderId)
                                    <a href="{{ route('material.issue.order.pdf', $reportOrderId) }}" target="_blank" class="catalog-button catalog-reports-button">Звіт по замовленню</a>
                                @else
                                    <button type="button" disabled class="catalog-button catalog-reports-button cursor-not-allowed opacity-50">Звіт по замовленню</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </x-modal>
                <x-modal name="create-manual-issuance" maxWidth="2xl" focusable>
                    <div role="dialog" aria-modal="true" aria-labelledby="create-manual-issuance-title">
                        <div class="flex items-center justify-between gap-3 border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4">
                            <h3 id="create-manual-issuance-title" class="text-xl font-bold text-[#174a47]">Видача матеріалів без норм</h3>
                            <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити" class="rounded-md p-2 text-[#245b53] hover:bg-white">
                                <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <livewire:manual-issuance-material-page :in-modal="true" />
                    </div>
                </x-modal>
                <div class="overflow-hidden rounded-lg border border-[#a8c8c5] bg-white shadow-sm">
                <div class="overflow-x-auto">
                <table class="catalog-table">
                    <thead>
                    <tr>
                        <th scope="col" class="issuance-id-column">ID</th>
                        <th scope="col" class="issuance-date-column">Дата</th>
                        <th scope="col">Замовлення</th>
                        <th scope="col">Отримав</th>
                        <th scope="col">Матеріал</th>
                        <th scope="col">Кількість</th>

                        <th scope="col">Звіт</th>
                    </tr>
                    </thead>
                    <tbody>

                    @forelse($items as $item)
                        <tr>
                            <td class="issuance-id-column">{{ $item->id }}</td>
                            <td class="issuance-date-column">
                                <span class="block whitespace-nowrap">{{ $item->created_at?->format('d.m.Y') }}</span>
                                <span class="block whitespace-nowrap">{{ $item->created_at?->format('H:i:s') }}</span>
                            </td>
                            <td>{{ $item->order_name?->name ?? '—' }}</td>
                            <td>{{$item->receivedByUser->name}}</td>
                            <td>
                                {{ $item->items->first()?->importMaterial?->name }}                            </td>
                            <td>{{ $item->items->first()?->quantity }}</td>

                            <td>
                                <a
                                    href="{{ route('manual-issuance-materials.pdf', $item->id) }}"
                                    target="_blank"
                                    class="catalog-button catalog-button-secondary"
                                >
                                    PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center">
                                Немає документів
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                </div>

                <div class="border-t border-[#a8c8c5] bg-white px-4 py-4">
                    <p class="mb-3 text-sm text-slate-600">Записів: {{ $items->total() }}</p>
                    {{ $items->links() }}
                </div>

            </div>

        </div>
    </div>
</div>
