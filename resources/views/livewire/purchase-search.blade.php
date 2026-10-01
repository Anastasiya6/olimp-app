<div class="purchases-page" x-data="{ deleteId: null, deleteName: '', deleting: false, deleteError: '' }" x-on:purchase-edit-open.window="$dispatch('open-modal', 'edit-purchase')">
    <x-catalog.panel class="mb-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
            <div class="w-full sm:w-56">
                <label for="purchase-name-search" class="compact-search-label catalog-label">Куди</label>
                <input id="purchase-name-search" type="search" wire:model.live="searchTerm" placeholder="Номер виробу — куди" class="compact-search catalog-input" />
            </div>
            <div class="w-full sm:w-56">
                <label for="purchase-entry-search" class="compact-search-label catalog-label">Що</label>
                <input id="purchase-entry-search" type="search" wire:model.live="searchTermChto" placeholder="Номер складової — що" class="compact-search catalog-input" />
            </div>
            <x-catalog.button x-on:click="$dispatch('open-modal', 'create-purchase')" aria-haspopup="dialog" variant="primary" class="catalog-add-button shrink-0 lg:ml-auto">
                <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                </svg>
                Додати покупну деталь
            </x-catalog.button>
        </div>
    </x-catalog.panel>
    <x-catalog.table :items="$items">
        <x-slot:head>
            <tr>
                <th scope="col">Куди</th>
                <th scope="col">Що</th>
                <th scope="col">Покупна</th>
                <th scope="col" class="text-right">Кількість</th>
                <th scope="col">Код 1С</th>
                <th scope="col">Замовлення</th>
                <th scope="col">Дії</th>
            </tr>
        </x-slot:head>
        @forelse($items as $item)
            <tr wire:key="purchase-row-{{ $item->id }}">
                <td>{{ $item->designation->designation ?? '—' }}</td>
                <td>{{ $item->designationEntry->designation ?? '—' }}</td>
                <td>{{ $item->purchase ?? '—' }}</td>
                <td class="whitespace-nowrap text-right tabular-nums">{{ $item->quantity ?? '—' }}</td>
                <td>{{ $item->code_1c ?? '—' }}</td>
                <td>{{ $item->order_names->implode('name', ', ') }}</td>
                <td>
                    <div class="flex flex-wrap gap-2">
                        <x-catalog.button wire:click="editPurchase({{ $item->id }})" wire:loading.attr="disabled" wire:target="editPurchase" aria-haspopup="dialog">Редагувати</x-catalog.button>
                        <x-catalog.button variant="danger" ::disabled="deleting"
                            data-delete-name="{{ ($item->designation->designation ?? '').' — '.($item->designationEntry->designation ?? '') }}"
                            x-on:click="deleteId = {{ $item->id }}; deleteName = $el.dataset.deleteName; deleteError = ''; $dispatch('open-modal', 'delete-purchase')">Видалити</x-catalog.button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-10 text-center text-base font-normal text-slate-700">
                    {{ trim($searchTerm ?? '') !== '' || trim($searchTermChto ?? '') !== '' ? 'За вашим запитом покупних деталей не знайдено.' : 'Покупних деталей поки немає.' }}
                </td>
            </tr>
        @endforelse
    </x-catalog.table>
    @foreach(['create' => 'Додати покупну деталь', 'edit' => 'Редагувати покупну деталь'] as $mode => $modalTitle)
        <div wire:key="{{ $mode }}-purchase-dialog">
            <x-modal :name="$mode.'-purchase'" maxWidth="2xl" :show="(bool) old('_purchase_'.$mode) && $errors->any()" focusable>
                <div role="dialog" aria-modal="true" aria-labelledby="{{ $mode }}-purchase-title">
                    <div class="flex items-start justify-between gap-4 border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4 sm:px-6">
                        <div>
                            <h3 id="{{ $mode }}-purchase-title" class="text-xl font-bold text-[#174a47]">{{ $modalTitle }}</h3>
                            <p class="mt-1 text-base text-slate-600">{{ $mode === 'create' ? 'Оберіть деталі та вкажіть дані покупної заміни.' : 'Змініть дані покупної деталі та замовлення.' }}</p>
                        </div>
                        <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити форму покупної деталі"
                            class="rounded-md p-2 text-[#245b53] hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-600">
                            <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    @if($mode === 'create' || $editingPurchase)
                        <div class="p-5 sm:p-6" wire:key="{{ $mode }}-purchase-form-{{ $mode === 'edit' ? $editingPurchase->id.'-'.$editFormVersion : 'new' }}">
                            @include('admin.include.purchases.form', ['inModal' => true, 'purchase' => $mode === 'edit' ? $editingPurchase : null])
                        </div>
                    @endif
                </div>
            </x-modal>
        </div>
    @endforeach
    <div wire:key="delete-purchase-dialog">
        <x-catalog.delete-dialog name="delete-purchase" method="deletePurchase" />
    </div>
</div>
