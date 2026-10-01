<div class="specifications-page" x-data="{ deleteId: null, deleteName: '', deleting: false, deleteError: '' }" x-on:specification-edit-open.window="$dispatch('open-modal', 'edit-specification')">
    <x-catalog.panel class="mb-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
            <div class="w-full sm:w-56">
                <label for="specification-name-search" class="compact-search-label catalog-label">Куди</label>
                <input id="specification-name-search" type="search" wire:model.live="searchTerm" placeholder="Номер виробу — куди" class="compact-search catalog-input" />
            </div>
            <div class="w-full sm:w-56">
                <label for="specification-entry-search" class="compact-search-label catalog-label">Що</label>
                <input id="specification-entry-search" type="search" wire:model.live="searchTermChto" placeholder="Номер складової — що" class="compact-search catalog-input" />
            </div>
            <label class="flex items-center gap-2 py-3 text-sm font-semibold text-slate-800">
                <input type="checkbox" wire:model.live="exactMatch" class="rounded border-[#a8c8c5] text-teal-700 focus:ring-teal-600" />
                Точне співпадіння
            </label>
            <x-catalog.button x-on:click="$dispatch('open-modal', 'create-specification')" aria-haspopup="dialog" variant="primary" class="catalog-add-button shrink-0 lg:ml-auto">
                <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                </svg>
                Додати запис M0020
            </x-catalog.button>
        </div>
    </x-catalog.panel>
    <x-catalog.table :items="$specifications" title="Перелік M0020">
        <x-slot:head>
            <tr>
                <th scope="col">Куди</th>
                <th scope="col">Що</th>
                <th scope="col" class="text-right">Кількість</th>
                <th scope="col">Шифр</th>
                <th scope="col">Дії</th>
            </tr>
        </x-slot:head>
        @forelse($specifications as $item)
            <tr wire:key="specification-row-{{ $item->id }}">
                <td>{{ $item->designations->designation ?? '—' }}</td>
                <td>{{ $item->designationEntry->designation ?? '—' }}</td>
                <td class="whitespace-nowrap text-right tabular-nums">{{ $item->quantity ?? '—' }}</td>
                <td>{{ $item->category_code ?? '—' }}</td>
                <td>
                    <div class="flex flex-wrap gap-2">
                        <x-catalog.button wire:click="editSpecification({{ $item->id }})" wire:loading.attr="disabled" wire:target="editSpecification" aria-haspopup="dialog">Редагувати</x-catalog.button>
                        <x-catalog.button variant="danger" ::disabled="deleting"
                            data-delete-name="{{ ($item->designations->designation ?? '').' — '.($item->designationEntry->designation ?? '') }}"
                            x-on:click="deleteId = {{ $item->id }}; deleteName = $el.dataset.deleteName; deleteError = ''; $dispatch('open-modal', 'delete-specification')">Видалити</x-catalog.button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="py-10 text-center text-base font-normal text-slate-700">
                    {{ trim($searchTerm ?? '') !== '' || trim($searchTermChto ?? '') !== '' ? 'За вашим запитом записів M0020 не знайдено.' : 'Записів M0020 поки немає.' }}
                </td>
            </tr>
        @endforelse
    </x-catalog.table>
    @foreach(['create' => 'Додати запис M0020', 'edit' => 'Редагувати запис M0020'] as $mode => $modalTitle)
        <div wire:key="{{ $mode }}-specification-dialog">
            <x-modal :name="$mode.'-specification'" maxWidth="2xl" :show="(bool) old('_specification_'.$mode) && $errors->any()" focusable>
                <div role="dialog" aria-modal="true" aria-labelledby="{{ $mode }}-specification-title">
                    <div class="flex items-start justify-between gap-4 border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4 sm:px-6">
                        <div>
                            <h3 id="{{ $mode }}-specification-title" class="text-xl font-bold text-[#174a47]">{{ $modalTitle }}</h3>
                            <p class="mt-1 text-base text-slate-600">{{ $mode === 'create' ? 'Вкажіть, що і куди входить, кількість та шифр.' : 'Змініть кількість та шифр.' }}</p>
                        </div>
                        <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити форму запису M0020"
                            class="rounded-md p-2 text-[#245b53] hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-600">
                            <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    @if($mode === 'create' || $editingSpecification)
                        <div class="p-5 sm:p-6" wire:key="{{ $mode }}-specification-form-{{ $mode === 'edit' ? $editingSpecification->id.'-'.$editFormVersion : 'new' }}">
                            @include('admin.include.specifications.form', ['inModal' => true, 'specification' => $mode === 'edit' ? $editingSpecification : null])
                        </div>
                    @endif
                </div>
            </x-modal>
        </div>
    @endforeach
    <div wire:key="delete-specification-dialog">
        <x-catalog.delete-dialog name="delete-specification" method="deleteSpecification" />
    </div>
</div>
