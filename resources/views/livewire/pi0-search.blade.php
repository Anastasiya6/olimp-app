<div x-data="{}" x-on:pi0-edit-open.window="$dispatch('open-modal', 'edit-pi0')">
    <x-catalog.panel class="mb-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
            <div class="w-full sm:w-56">
                <label for="designation-number-search" class="compact-search-label catalog-label">Номер</label>
                <input id="designation-number-search" type="search" wire:model.live="searchTermChto" placeholder="Номер ПИ0 або його частина" class="compact-search catalog-input" />
            </div>
            <div class="w-full sm:w-56">
                <label for="designation-name-search" class="compact-search-label catalog-label">Назва або ГОСТ</label>
                <input id="designation-name-search" type="search" wire:model.live="searchTerm" placeholder="Назва або ГОСТ" class="compact-search catalog-input" />
            </div>
            <x-catalog.button :href="route('pi0.all')" target="_blank" rel="noopener" class="shrink-0 lg:ml-auto">ПИ0 у PDF</x-catalog.button>
            <x-catalog.button x-on:click="$dispatch('open-modal', 'create-pi0')" aria-haspopup="dialog" variant="primary" class="catalog-add-button shrink-0 lg:ml-auto">
                <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                </svg>
                Додати ПИ0
            </x-catalog.button>
        </div>
    </x-catalog.panel>
    <x-catalog.table :items="$items" title="Перелік ПИ0">
        <x-slot:head>
            <tr>
                <th scope="col" aria-sort="{{ $sortField === 'designation' ? ($sortAsc ? 'ascending' : 'descending') : 'none' }}">
                    <button type="button" wire:click="sortBy('designation')" class="flex items-center gap-2 rounded-md text-left font-bold focus:outline-none focus:ring-2 focus:ring-teal-600">
                        Номер
                        <x-sort-icon field="designation" :sortField="$sortField" :sortAsc="$sortAsc" />
                    </button>
                </th>
                <th scope="col">Назва</th>
                <th scope="col">ГОСТ</th>
                <th scope="col">Од. виміру</th>
                <th scope="col">Код 1С</th>
                <th scope="col">Дії</th>
            </tr>
        </x-slot:head>
        @forelse($items as $item)
            <tr wire:key="pi0-row-{{ $item->id }}">
                <td class="whitespace-nowrap">{{ $item->designation ?? '—' }}</td>
                <td><div class="min-w-[16rem] max-w-xl break-words">{{ $item->name ?? '—' }}</div></td>
                <td><div class="max-w-xs break-words">{{ $item->gost ?? '—' }}</div></td>
                <td class="whitespace-nowrap">{{ $item->unit->unit ?? '—' }}</td>
                <td class="whitespace-nowrap tabular-nums">{{ $item->code_1c ?? '—' }}</td>
                <td>
                    <x-catalog.button wire:click="editPi0({{ $item->id }})" wire:loading.attr="disabled" wire:target="editPi0" aria-haspopup="dialog">Редагувати</x-catalog.button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="py-10 text-center text-base font-normal text-slate-700">
                    {{ trim($searchTerm ?? '') !== '' || trim($searchTermChto ?? '') !== '' ? 'За вашим запитом записів ПИ0 не знайдено.' : 'Записів ПИ0 поки немає.' }}
                </td>
            </tr>
        @endforelse
    </x-catalog.table>
    @foreach(['create' => 'Додати ПИ0', 'edit' => 'Редагувати ПИ0'] as $mode => $modalTitle)
        <div wire:key="{{ $mode }}-pi0-dialog">
            <x-modal :name="$mode.'-pi0'" maxWidth="2xl" :show="(bool) old('_pi0_'.$mode) && $errors->any()" focusable>
                <div role="dialog" aria-modal="true" aria-labelledby="{{ $mode }}-pi0-title">
                    <div class="flex items-start justify-between gap-4 border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4 sm:px-6">
                        <div>
                            <h3 id="{{ $mode }}-pi0-title" class="text-xl font-bold text-[#174a47]">{{ $modalTitle }}</h3>
                            <p class="mt-1 text-base text-slate-600">{{ $mode === 'create' ? 'Вкажіть номер, назву, ГОСТ та код 1С.' : 'Змініть дані ПИ0 та збережіть зміни.' }}</p>
                        </div>
                        <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити форму ПИ0"
                            class="rounded-md p-2 text-[#245b53] hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-600">
                            <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    @if($mode === 'create' || $editingPi0)
                        <div class="p-5 sm:p-6" wire:key="{{ $mode }}-pi0-form-{{ $mode === 'edit' ? $editingPi0->id.'-'.$editFormVersion : 'new' }}">
                            @include('admin.include.pi0s.form', ['inModal' => true, 'designation' => $mode === 'edit' ? $editingPi0 : null])
                        </div>
                    @endif
                </div>
            </x-modal>
        </div>
    @endforeach
</div>
