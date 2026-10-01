<div class="order-names-page" x-data="{ deleteId: null, deleteName: '', deleting: false, deleteError: '' }" x-on:order-name-edit-open.window="$dispatch('open-modal', 'edit-order-name')">
    <x-catalog.panel class="mb-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
            <div class="w-full sm:w-56">
                <label for="order-name-name-search" class="compact-search-label catalog-label">Замовлення</label>
                <input id="order-name-name-search" type="search" wire:model.live="searchTerm" placeholder="Назва або номер замовлення" class="compact-search catalog-input" />
            </div>
            <x-catalog.button x-on:click="$dispatch('open-modal', 'create-order-name')" aria-haspopup="dialog" variant="primary" class="catalog-add-button shrink-0 lg:ml-auto">
                <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                </svg>
                Додати замовлення
            </x-catalog.button>
        </div>
    </x-catalog.panel>
    <x-catalog.table :items="$items">
        <x-slot:head>
            <tr>
                <th scope="col">Замовлення</th>
                <th scope="col">Кількість комплектів</th>
                <th scope="col">Є замовленням</th>
                <th scope="col">Дії</th>
            </tr>
        </x-slot:head>
        @forelse($items as $item)
            <tr wire:key="order-name-row-{{ $item->id }}">
                <td>{{ $item->name ?? '—' }}</td>
                <td>{{ $item->quantity ?? '—' }}</td>
                <td>{{ $item->is_order ? 'Так' : 'Ні' }}</td>
                <td>
                    <div class="flex flex-wrap gap-2">
                        <x-catalog.button wire:click="editOrderName({{ $item->id }})" wire:loading.attr="disabled" wire:target="editOrderName" aria-haspopup="dialog">Редагувати</x-catalog.button>
                        <x-catalog.button variant="danger" ::disabled="deleting"
                            data-delete-name="{{ $item->name }}"
                            x-on:click="deleteId = {{ $item->id }}; deleteName = $el.dataset.deleteName; deleteError = ''; $dispatch('open-modal', 'delete-order-name')">Видалити</x-catalog.button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="py-10 text-center text-base font-normal text-slate-700">
                    {{ trim($searchTerm ?? '') !== '' ? 'За вашим запитом замовлень не знайдено.' : 'Замовлень поки немає.' }}
                </td>
            </tr>
        @endforelse
    </x-catalog.table>
    @foreach(['create' => 'Додати замовлення', 'edit' => 'Редагувати замовлення'] as $mode => $modalTitle)
        <div wire:key="{{ $mode }}-order-name-dialog">
            <x-modal :name="$mode.'-order-name'" maxWidth="2xl" :show="(bool) old('_order_name_'.$mode) && $errors->any()" focusable>
                <div role="dialog" aria-modal="true" aria-labelledby="{{ $mode }}-order-name-title">
                    <div class="flex items-start justify-between gap-4 border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4 sm:px-6">
                        <div>
                            <h3 id="{{ $mode }}-order-name-title" class="text-xl font-bold text-[#174a47]">{{ $modalTitle }}</h3>
                            <p class="mt-1 text-base text-slate-600">{{ $mode === 'create' ? 'Вкажіть назву, кількість комплектів та ознаку замовлення.' : 'Змініть дані замовлення та збережіть зміни.' }}</p>
                        </div>
                        <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити форму замовлення"
                            class="rounded-md p-2 text-[#245b53] hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-600">
                            <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    @if($mode === 'create' || $editingOrderName)
                        <div class="p-5 sm:p-6" wire:key="{{ $mode }}-order-name-form-{{ $mode === 'edit' ? $editingOrderName->id.'-'.$editFormVersion : 'new' }}">
                            @include('admin.include.order-names.form', ['inModal' => true, 'orderName' => $mode === 'edit' ? $editingOrderName : null])
                        </div>
                    @endif
                </div>
            </x-modal>
        </div>
    @endforeach
    <div wire:key="delete-order-name-dialog">
        <x-catalog.delete-dialog name="delete-order-name" method="deleteOrderName" />
    </div>
</div>
