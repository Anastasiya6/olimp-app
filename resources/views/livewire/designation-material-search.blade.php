<div class="norms-page" x-data="{ deleteId: null, deleteName: '', deleting: false, deleteError: '' }" x-on:norm-edit-open.window="$dispatch('open-modal', 'edit-norm')">
    <x-catalog.panel class="mb-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
            <div class="w-full sm:w-56">
                <label for="norm-designation-search" class="compact-search-label catalog-label">Деталь</label>
                <input id="norm-designation-search" type="search" wire:model.live="searchTerm" placeholder="Номер деталі або його частина" class="compact-search catalog-input" />
            </div>
            <div class="w-full sm:w-56">
                <label for="norm-material-search" class="compact-search-label catalog-label">Матеріал</label>
                <input id="norm-material-search" type="search" wire:model.live="searchTermMaterial" placeholder="Назва матеріалу" class="compact-search catalog-input" />
            </div>
            <x-catalog.button x-on:click="$dispatch('open-modal', 'create-norm')" aria-haspopup="dialog" variant="primary" class="catalog-add-button shrink-0 lg:ml-auto">
                <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                </svg>
                Додати норму
            </x-catalog.button>
        </div>
    </x-catalog.panel>
    <x-catalog.table :items="$items" title="Норми витрат матеріалів">
        <x-slot:head>
            <tr>
                <th scope="col">Деталь</th>
                <th scope="col">Матеріал</th>
                <th scope="col" class="text-right">Норма</th>
                <th scope="col" class="text-center">Цех</th>
                <th scope="col">Дії</th>
            </tr>
        </x-slot:head>
        @forelse($items as $item)
            <tr wire:key="norm-row-{{ $item->id }}">
                <td class="whitespace-nowrap">{{ $item->designation->designation ?? '—' }}</td>
                <td><div class="min-w-[16rem] max-w-xl break-words">{{ $item->material->name ?? '—' }}</div></td>
                <td class="whitespace-nowrap text-right tabular-nums">{{ $item->norm ?? '—' }}</td>
                <td class="whitespace-nowrap text-center">{{ $item->department->number ?? '—' }}</td>
                <td>
                    <div class="flex flex-wrap gap-2">
                        <x-catalog.button wire:click="editNorm({{ $item->id }})" wire:loading.attr="disabled" wire:target="editNorm" aria-haspopup="dialog">Редагувати</x-catalog.button>
                        <x-catalog.button variant="danger" ::disabled="deleting"
                            data-delete-name="{{ ($item->designation->designation ?? '').' — '.($item->material->name ?? '') }}"
                            x-on:click="deleteId = {{ $item->id }}; deleteName = $el.dataset.deleteName; deleteError = ''; $dispatch('open-modal', 'delete-norm')">Видалити</x-catalog.button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="py-10 text-center text-base font-normal text-slate-700">
                    {{ trim($searchTerm ?? '') !== '' || trim($searchTermMaterial ?? '') !== '' ? 'За вашим запитом норм не знайдено.' : 'Норм поки немає.' }}
                </td>
            </tr>
        @endforelse
    </x-catalog.table>
    @foreach(['create' => 'Додати норму', 'edit' => 'Редагувати норму'] as $mode => $modalTitle)
        <div wire:key="{{ $mode }}-norm-dialog">
            <x-modal :name="$mode.'-norm'" maxWidth="2xl" :show="(bool) old('_norm_'.$mode) && $errors->any()" focusable>
                <div role="dialog" aria-modal="true" aria-labelledby="{{ $mode }}-norm-title">
                    <div class="flex items-start justify-between gap-4 border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4 sm:px-6">
                        <div>
                            <h3 id="{{ $mode }}-norm-title" class="text-xl font-bold text-[#174a47]">{{ $modalTitle }}</h3>
                            <p class="mt-1 text-base text-slate-600">{{ $mode === 'create' ? 'Оберіть деталь і матеріал, вкажіть норму витрат та цех.' : 'Змініть матеріал, норму витрат або цех.' }}</p>
                        </div>
                        <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити форму норми"
                            class="rounded-md p-2 text-[#245b53] hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-600">
                            <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    @if($mode === 'create' || $editingNorm)
                        <div class="p-5 sm:p-6" wire:key="{{ $mode }}-norm-form-{{ $mode === 'edit' ? $editingNorm->id.'-'.$editFormVersion : 'new' }}">
                            @include('admin.include.designation-material.form', ['inModal' => true, 'designationMaterial' => $mode === 'edit' ? $editingNorm : null])
                        </div>
                    @endif
                </div>
            </x-modal>
        </div>
    @endforeach
    <div wire:key="delete-norm-dialog">
        <x-catalog.delete-dialog name="delete-norm" method="deleteDesignationMaterial" />
    </div>
</div>
