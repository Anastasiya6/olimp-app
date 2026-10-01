<div class="group-materials-page" x-data="{ deleteId: null, deleteName: '', deleting: false, deleteError: '' }" x-on:group-material-edit-open.window="$dispatch('open-modal', 'edit-group-material')">
    <x-catalog.panel class="mb-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
            <div class="w-full sm:w-56">
                <label for="group-material-name-search" class="compact-search-label catalog-label">Матеріалокомплект</label>
                <input id="group-material-name-search" type="search" wire:model.live="searchTerm" placeholder="Назва матеріалокомплекту" class="compact-search catalog-input" />
            </div>
            <x-catalog.button x-on:click="$dispatch('open-modal', 'create-group-material')" aria-haspopup="dialog" variant="primary" class="catalog-add-button shrink-0 lg:ml-auto">
                <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                </svg>
                Додати матеріалокомплект
            </x-catalog.button>
        </div>
    </x-catalog.panel>
    <x-catalog.table :items="$items" title="Склад матеріалокомплектів">
        <x-slot:head>
            <tr>
                <th scope="col">Матеріалокомплект</th>
                <th scope="col">Матеріал</th>
                <th scope="col" class="text-right">Норма</th>
                <th scope="col">Дії</th>
            </tr>
        </x-slot:head>
        @forelse($items as $item)
            <tr wire:key="group-material-row-{{ $item->id }}">
                <td>{{ $item->material->name ?? '—' }}</td>
                <td>{{ $item->materialEntry->name ?? '—' }}</td>
                <td class="whitespace-nowrap text-right tabular-nums">{{ $item->norm ?? '—' }}</td>
                <td>
                    <div class="flex flex-wrap gap-2">
                        <x-catalog.button wire:click="editGroupMaterial({{ $item->id }})" wire:loading.attr="disabled" wire:target="editGroupMaterial" aria-haspopup="dialog">Редагувати</x-catalog.button>
                        <x-catalog.button variant="danger" ::disabled="deleting"
                            data-delete-name="{{ ($item->material->name ?? '').' — '.($item->materialEntry->name ?? '') }}"
                            x-on:click="deleteId = {{ $item->id }}; deleteName = $el.dataset.deleteName; deleteError = ''; $dispatch('open-modal', 'delete-group-material')">Видалити</x-catalog.button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="py-10 text-center text-base font-normal text-slate-700">
                    {{ trim($searchTerm ?? '') !== '' ? 'За вашим запитом матеріалокомплектів не знайдено.' : 'Матеріалокомплектів поки немає.' }}
                </td>
            </tr>
        @endforelse
    </x-catalog.table>
    @foreach(['create' => 'Додати матеріалокомплект', 'edit' => 'Редагувати матеріалокомплект'] as $mode => $modalTitle)
        <div wire:key="{{ $mode }}-group-material-dialog">
            <x-modal :name="$mode.'-group-material'" maxWidth="2xl" :show="(bool) old('_group_material_'.$mode) && $errors->any()" focusable>
                <div role="dialog" aria-modal="true" aria-labelledby="{{ $mode }}-group-material-title">
                    <div class="flex items-start justify-between gap-4 border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4 sm:px-6">
                        <div>
                            <h3 id="{{ $mode }}-group-material-title" class="text-xl font-bold text-[#174a47]">{{ $modalTitle }}</h3>
                            <p class="mt-1 text-base text-slate-600">{{ $mode === 'create' ? 'Оберіть матеріалокомплект, матеріал та вкажіть норму витрат.' : 'Змініть норму витрат матеріалу.' }}</p>
                        </div>
                        <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити форму матеріалокомплекту"
                            class="rounded-md p-2 text-[#245b53] hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-600">
                            <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    @if($mode === 'create' || $editingGroupMaterial)
                        <div class="p-5 sm:p-6" wire:key="{{ $mode }}-group-material-form-{{ $mode === 'edit' ? $editingGroupMaterial->id.'-'.$editFormVersion : 'new' }}">
                            @include('admin.include.group-materials.form', ['inModal' => true, 'groupMaterial' => $mode === 'edit' ? $editingGroupMaterial : null])
                        </div>
                    @endif
                </div>
            </x-modal>
        </div>
    @endforeach
    <div wire:key="delete-group-material-dialog">
        <x-catalog.delete-dialog name="delete-group-material" method="deleteGroupMaterial" />
    </div>
</div>
