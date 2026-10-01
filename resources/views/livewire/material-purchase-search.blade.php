<div class="purchases-page" x-data="{ deleteId: null, deleteName: '', deleting: false, deleteError: '' }" x-on:material-purchase-edit-open.window="$dispatch('open-modal', 'edit-material-purchase')">
    <x-catalog.panel class="mb-5">
        <div class="flex flex-wrap items-end gap-3">
            <div class="w-full sm:w-56">
                <label for="material-purchase-name-search" class="compact-search-label mb-1 block text-sm font-medium text-slate-600">Куди</label>
                <input id="material-purchase-name-search" type="search" wire:model.live="searchTerm" placeholder="Куди — номер виробу" class="compact-search block h-9 w-full rounded-md border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-teal-600 focus:ring-1 focus:ring-teal-600" />
            </div>
            <div class="w-full sm:w-56">
                <label for="material-purchase-entry-search" class="compact-search-label mb-1 block text-sm font-medium text-slate-600">Що</label>
                <input id="material-purchase-entry-search" type="search" wire:model.live="searchTermChto" placeholder="Що — номер складової" class="compact-search block h-9 w-full rounded-md border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-teal-600 focus:ring-1 focus:ring-teal-600" />
            </div>
            <x-catalog.button x-on:click="$dispatch('open-modal', 'create-material-purchase')" aria-haspopup="dialog" variant="primary" class="catalog-add-button shrink-0 lg:ml-auto">
                <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                </svg>
                Додати заміну матеріалу
            </x-catalog.button>
        </div>
    </x-catalog.panel>
    <x-catalog.table :items="$items">
        <x-slot:head>
            <tr>
                <th scope="col">Куди</th>
                <th scope="col">Що</th>
                <th scope="col">Матеріал</th>
                <th scope="col" class="text-right">Кількість</th>
                <th scope="col">Код 1С</th>
                <th scope="col">Замовлення</th>
                <th scope="col">Дії</th>
            </tr>
        </x-slot:head>
        @forelse($items as $item)
            <tr wire:key="material-purchase-row-{{ $item->id }}">
                <td>{{ $item->designation->designation ?? '—' }}</td>
                <td>{{ $item->designationEntry->designation ?? '—' }}</td>
                <td>{{ $item->material->name ?? '—' }}</td>
                <td class="whitespace-nowrap text-right tabular-nums">{{ $item->norm ?? '—' }}</td>
                <td>{{ $item->code_1c ?? '—' }}</td>
                <td>{{ $item->order_names->implode('name', ', ') }}</td>
                <td>
                    <div class="flex flex-wrap gap-2">
                        <x-catalog.button wire:click="editMaterialPurchase({{ $item->id }})" wire:loading.attr="disabled" wire:target="editMaterialPurchase" aria-haspopup="dialog">Редагувати</x-catalog.button>
                        <x-catalog.button variant="danger" ::disabled="deleting"
                            data-delete-name="{{ ($item->designation->designation ?? '').' — '.($item->designationEntry->designation ?? '') }}"
                            x-on:click="deleteId = {{ $item->id }}; deleteName = $el.dataset.deleteName; deleteError = ''; $dispatch('open-modal', 'delete-material-purchase')">Видалити</x-catalog.button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-10 text-center text-base font-normal text-slate-700">
                    {{ trim($searchTerm ?? '') !== '' || trim($searchTermChto ?? '') !== '' ? 'За вашим запитом замін матеріалів не знайдено.' : 'Замін матеріалів поки немає.' }}
                </td>
            </tr>
        @endforelse
    </x-catalog.table>
    @foreach(['create' => 'Додати заміну матеріалу', 'edit' => 'Редагувати заміну матеріалу'] as $mode => $modalTitle)
        <div wire:key="{{ $mode }}-material-purchase-dialog">
            <x-modal :name="$mode.'-material-purchase'" maxWidth="2xl" :show="(bool) old('_material_purchase_'.$mode) && $errors->any()" focusable>
                <div role="dialog" aria-modal="true" aria-labelledby="{{ $mode }}-material-purchase-title">
                    <div class="flex items-start justify-between gap-4 border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4 sm:px-6">
                        <div>
                            <h3 id="{{ $mode }}-material-purchase-title" class="text-xl font-bold text-[#174a47]">{{ $modalTitle }}</h3>
                            <p class="mt-1 text-base text-slate-600">{{ $mode === 'create' ? 'Оберіть деталі та вкажіть дані заміни матеріалу.' : 'Змініть дані заміни матеріалу та замовлення.' }}</p>
                        </div>
                        <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити форму заміни матеріалу"
                            class="rounded-md p-2 text-[#245b53] hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-600">
                            <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    @if($mode === 'create' || $editingMaterialPurchase)
                        <div class="p-5 sm:p-6" wire:key="{{ $mode }}-material-purchase-form-{{ $mode === 'edit' ? $editingMaterialPurchase->id.'-'.$editFormVersion : 'new' }}">
                            @include('admin.include.material-purchases.form', ['inModal' => true, 'purchase' => $mode === 'edit' ? $editingMaterialPurchase : null])
                        </div>
                    @endif
                </div>
            </x-modal>
        </div>
    @endforeach
    <div wire:key="delete-material-purchase-dialog">
        <x-catalog.delete-dialog name="delete-material-purchase" method="deleteMaterialPurchase" />
    </div>
</div>
