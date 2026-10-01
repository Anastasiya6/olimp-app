<div x-data="{ deleteId: null, deleteName: '', deleting: false, deleteError: '' }" x-on:material-edit-open.window="$dispatch('open-modal', 'edit-material')">
    <x-catalog.panel class="mb-5">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="w-full sm:w-56">
                <label for="material-name-search" class="compact-search-label mb-2 block text-base font-semibold text-slate-800">Пошук матеріалу</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <circle cx="10.5" cy="10.5" r="6.5" />
                            <path stroke-linecap="round" d="m16 16 4 4" />
                        </svg>
                    </span>
                    <input id="material-name-search" type="search" wire:model.live="searchTerm" placeholder="Введіть назву матеріалу"
                        class="compact-search block h-12 w-full rounded-md border-[#a8c8c5] bg-[#f7fbfa] pl-10 text-base text-slate-900 placeholder:text-slate-500 focus:border-teal-600 focus:ring-teal-600" />
                </div>
            </div>
            <button type="button" x-on:click="$dispatch('open-modal', 'create-material')" aria-haspopup="dialog"
                class="catalog-add-button inline-flex h-12 items-center justify-center gap-2 rounded-md border border-[#286357] bg-[#286357] px-4 text-base font-semibold text-white shadow-sm transition hover:bg-[#1e5046] focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                </svg>
                Додати матеріал
            </button>
        </div>
    </x-catalog.panel>
    <x-catalog.table :items="$items">
        <x-slot:head>
                    <tr>
                        <th scope="col" class=" text-left font-bold">Назва матеріалу</th>
                        <th scope="col" class="whitespace-nowrap  text-center font-bold">Одиниця виміру</th>
                        <th scope="col" class="whitespace-nowrap  text-left font-bold">Код 1С</th>
                        <th scope="col" class=" text-left font-bold">Дії</th>
                    </tr>
                        </x-slot:head>
                    @forelse($items as $item)
                        <tr wire:key="material-row-{{ $item->id }}">
                            <td>
                                <div class="min-w-[16rem] max-w-xl break-words">{{ $item->name ?? '—' }}</div>
                            </td>
                            <td class="whitespace-nowrap  text-center">{{ $item->unit->unit ?? '—' }}</td>
                            <td class="whitespace-nowrap  tabular-nums">{{ $item->code_1c ?? '—' }}</td>
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" wire:click="editMaterial({{ $item->id }})" wire:loading.attr="disabled" wire:target="editMaterial" aria-haspopup="dialog"
                                        class="inline-flex min-h-[44px] items-center rounded-md border border-[#a8c8c5] bg-white px-3 py-2 text-base font-semibold text-[#245b53] hover:bg-[#edf6f3] focus:outline-none focus:ring-2 focus:ring-teal-600 disabled:opacity-50">Редагувати</button>
                                    <button type="button" :disabled="deleting"
                                        x-on:click="deleteId = {{ $item->id }}; deleteName = @js($item->name); deleteError = ''; $dispatch('open-modal', 'delete-material')"
                                        class="inline-flex min-h-[44px] items-center rounded-md border border-red-200 bg-white px-3 py-2 text-base font-semibold text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500">Видалити</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-base text-slate-700">
                                {{ trim($searchTerm ?? '') !== '' ? 'За вашим запитом матеріалів не знайдено.' : 'Матеріалів поки немає.' }}
                            </td>
                        </tr>
                    @endforelse
                    </x-catalog.table>
    <div wire:key="create-material-dialog">
        <x-modal name="create-material" maxWidth="2xl" :show="(bool) old('_material_create') && $errors->any()" focusable>
            <div role="dialog" aria-modal="true" aria-labelledby="create-material-title">
                <div class="flex items-start justify-between gap-4 border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4 sm:px-6">
                    <div>
                        <h3 id="create-material-title" class="text-xl font-bold text-[#174a47]">Додати матеріал</h3>
                        <p class="mt-1 text-base text-slate-600">Назва, одиниця виміру та код 1С.</p>
                    </div>
                    <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити додавання матеріалу"
                        class="rounded-md p-2 text-[#245b53] hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-600">
                        <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-5 sm:p-6">
                    @include('admin.include.materials.create-form', ['inModal' => true])
                </div>
            </div>
        </x-modal>
    </div>
    <div wire:key="edit-material-dialog">
        <x-modal name="edit-material" maxWidth="2xl" focusable>
            <div role="dialog" aria-modal="true" aria-labelledby="edit-material-title">
                <div class="flex items-start justify-between gap-4 border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4 sm:px-6">
                    <div>
                        <h3 id="edit-material-title" class="text-xl font-bold text-[#174a47]">Редагувати матеріал</h3>
                        <p class="mt-1 text-base text-slate-600">Змініть дані та збережіть матеріал.</p>
                    </div>
                    <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити редагування матеріалу"
                        class="rounded-md p-2 text-[#245b53] hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-600">
                        <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                @if($editingMaterial)
                    <div class="p-5 sm:p-6" wire:key="edit-material-form-{{ $editingMaterial->id }}-{{ $editFormVersion }}">
                        @include('admin.include.materials.edit-form', ['material' => $editingMaterial, 'inModal' => true])
                    </div>
                @endif
            </div>
        </x-modal>
    </div>
    <div wire:key="material-delete-dialog">
        <x-catalog.delete-dialog name="delete-material" method="deleteMaterial" />
    </div>
</div>
