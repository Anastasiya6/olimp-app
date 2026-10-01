<div x-data="{}" x-on:designation-edit-open.window="$dispatch('open-modal', 'edit-designation')">
    <x-catalog.panel class="mb-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
            <div class="w-full sm:w-56">
                <label for="designation-number-search" class="compact-search-label catalog-label">Номер</label>
                <input id="designation-number-search" type="search" wire:model.live="searchTermChto" placeholder="Номер виробу або його частина" class="compact-search catalog-input" />
            </div>
            <div class="w-full sm:w-56">
                <label for="designation-name-search" class="compact-search-label catalog-label">Назва</label>
                <input id="designation-name-search" type="search" wire:model.live="searchTerm" placeholder="Назва виробу" class="compact-search catalog-input" />
            </div>
            <x-catalog.button x-on:click="$dispatch('open-modal', 'create-designation')" aria-haspopup="dialog" variant="primary" class="catalog-add-button shrink-0 lg:ml-auto">
                <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                </svg>
                Додати виріб
            </x-catalog.button>
        </div>
    </x-catalog.panel>
    <x-catalog.table :items="$items" title="Перелік виробів">
        <x-slot:head>
            <tr>
                <th scope="col">Номер</th>
                <th scope="col">Назва</th>
                <th scope="col">Маршрут</th>
                <th scope="col">Код 1С</th>
                <th scope="col">Дії</th>
            </tr>
        </x-slot:head>
        @forelse($items as $item)
            <tr wire:key="designation-row-{{ $item->id }}">
                <td class="whitespace-nowrap">{{ $item->designation ?? '—' }}</td>
                <td><div class="min-w-[16rem] max-w-xl break-words">{{ $item->name ?? '—' }}</div></td>
                <td><div class="max-w-xs break-words">{{ $item->route ?? '—' }}</div></td>
                <td class="whitespace-nowrap tabular-nums">{{ $item->code_1c ?? '—' }}</td>
                <td>
                    <x-catalog.button wire:click="editDesignation({{ $item->id }})" wire:loading.attr="disabled" wire:target="editDesignation" aria-haspopup="dialog">Редагувати</x-catalog.button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="py-10 text-center text-base font-normal text-slate-700">
                    {{ trim($searchTerm ?? '') !== '' || trim($searchTermChto ?? '') !== '' ? 'За вашим запитом виробів не знайдено.' : 'Виробів поки немає.' }}
                </td>
            </tr>
        @endforelse
    </x-catalog.table>
    @foreach(['create' => 'Додати виріб', 'edit' => 'Редагувати виріб'] as $mode => $modalTitle)
        <div wire:key="{{ $mode }}-designation-dialog">
            <x-modal :name="$mode.'-designation'" maxWidth="2xl" :show="(bool) old('_designation_'.$mode) && $errors->any()" focusable>
                <div role="dialog" aria-modal="true" aria-labelledby="{{ $mode }}-designation-title">
                    <div class="flex items-start justify-between gap-4 border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4 sm:px-6">
                        <div>
                            <h3 id="{{ $mode }}-designation-title" class="text-xl font-bold text-[#174a47]">{{ $modalTitle }}</h3>
                            <p class="mt-1 text-base text-slate-600">{{ $mode === 'create' ? 'Вкажіть номер, назву, маршрут та код 1С.' : 'Змініть дані виробу та збережіть зміни.' }}</p>
                        </div>
                        <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити форму виробу"
                            class="rounded-md p-2 text-[#245b53] hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-600">
                            <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    @if($mode === 'create' || $editingDesignation)
                        <div class="p-5 sm:p-6" wire:key="{{ $mode }}-designation-form-{{ $mode === 'edit' ? $editingDesignation->id.'-'.$editFormVersion : 'new' }}">
                            @include('admin.include.designations.form', ['inModal' => true, 'designation' => $mode === 'edit' ? $editingDesignation : null])
                        </div>
                    @endif
                </div>
            </x-modal>
        </div>
    @endforeach
</div>
