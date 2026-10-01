<div class="issuance-materials-page" x-data="{}">

    <x-slot name="header" compact="true">
        <h2 class="text-xl font-bold leading-tight text-[#174a47]">
            Видача матеріалів без замовлення
        </h2>
    </x-slot>
    <div class="bg-[#f2f7f6] py-3 sm:py-4">
        <div class="mx-auto max-w-7xl px-3 sm:px-6 lg:px-8">

                <x-catalog.panel class="mb-4">
                    <div class="flex justify-end">
                        <x-catalog.button variant="primary" class="catalog-add-button" x-on:click="$dispatch('open-modal', 'create-manual-issuance')" aria-haspopup="dialog">
                            <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
                            Додати документ
                        </x-catalog.button>
                    </div>
                </x-catalog.panel>
                <x-modal name="create-manual-issuance" maxWidth="2xl" focusable>
                    <div role="dialog" aria-modal="true" aria-labelledby="create-manual-issuance-title">
                        <div class="flex items-center justify-between gap-3 border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4">
                            <h3 id="create-manual-issuance-title" class="text-xl font-bold text-[#174a47]">Видача матеріалів без замовлення</h3>
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
                        <th scope="col">Отримав</th>
                        <th scope="col">Матеріал</th>

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
                            <td>{{$item->receivedByUser->name}}</td>
                            <td>
                                {{ $item->items->first()?->importMaterial?->name }}                            </td>

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
                            <td colspan="5" class="p-4 text-center">
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
