<div class="material-stock-documents-page" x-data="{ pendingDocument: null, deleting: false, deleteError: '' }" x-on:material-surplus-open.window="$dispatch('open-modal', 'create-material-surplus')" x-on:material-surplus-close.window="$dispatch('close-modal', 'create-material-surplus')">
    <x-slot name="header" compact="true">
        <h2 class="text-xl font-bold leading-tight text-[#174a47]">Надлишки матеріалів</h2>
    </x-slot>

    <div class="bg-[#f2f7f6] py-3 sm:py-4">
        <div class="mx-auto max-w-7xl px-3 sm:px-6 lg:px-8">
            <div class="space-y-4">
    <x-catalog.panel>
        <div class="flex justify-end">
            <x-catalog.button wire:click="openCreate" aria-haspopup="dialog" variant="primary" class="catalog-add-button">
                <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                </svg>
                Додати надлишки
            </x-catalog.button>
        </div>
    </x-catalog.panel>

    @if(session()->has('success'))
        <div role="status" class="my-4 rounded-lg border border-[#a8c8c5] bg-[#e3f1ee] px-4 py-3 text-base font-medium text-[#174a47]">{{ session('success') }}</div>
    @endif

    <x-catalog.table :items="$surpluses">
        <x-slot:head>
            <tr>
                <th scope="col">№</th>
                <th scope="col" class="text-left">Дата створення</th>
                {{--
                <th scope="col" class="text-left document-author-column">Створив</th>
                <th scope="col" class="text-left document-author-column">Остання<br>зміна</th>
                --}}
                <th scope="col" class="text-left">Цех</th>
                <th scope="col" class="text-left">Артикул</th>
                <th scope="col" class="text-left">Матеріал</th>
                <th scope="col" class="text-right">Кількість надлишків</th>
                <th scope="col" class="text-center">Од. виміру</th>
                <th scope="col">Дії</th>
                <th scope="col">Проведення</th>
                <th scope="col">Видалення</th>
            </tr>
        </x-slot:head>
        @forelse($surpluses as $surplus)
            <tr wire:key="material-surplus-{{ $surplus->id }}">
                <td>{{ $surplus->id }}</td>
                <td>{{ $surplus->created_at?->format('d.m.Y H:i') }}</td>
                {{--
                <td class="document-author-column">{{ $surplus->creator?->name ?? '' }}</td>
                <td class="document-author-column">{{ $surplus->updater?->name ?? '' }}</td>
                --}}
                <td>{{ $surplus->department?->number ?? '—' }}</td>
                <td class="font-bold">{{ $surplus->material?->article ?? '—' }}</td>
                <td>{{ $surplus->material?->name ?? '—' }}</td>
                <td class="text-right font-bold tabular-nums">{{ $surplus->quantity }}</td>
                <td class="text-center">{{ $surplus->material?->unit?->unit ?? '—' }}</td>
                <td>
                    @if($surplus->status === 'draft')
                        <x-catalog.button wire:click="openEdit({{ $surplus->id }})" wire:loading.attr="disabled">Редагувати</x-catalog.button>
                    @endif
                </td>
                <td>
                    @if($surplus->status === 'draft')
                        <x-catalog.button wire:click="postDocument({{ $surplus->id }})" wire:loading.attr="disabled" variant="primary">Провести</x-catalog.button>
                    @else
                        <x-catalog.button wire:click="unpostDocument({{ $surplus->id }})" wire:loading.attr="disabled" variant="danger">Скасувати</x-catalog.button>
                    @endif
                </td>
                <td>
                    @if($surplus->status === 'draft')
                        <x-catalog.button x-on:click="pendingDocument = {{ $surplus->id }}; deleteError = ''; $dispatch('open-modal', 'confirm-material-surplus-delete')" x-bind:disabled="deleting" aria-haspopup="dialog" variant="danger">Видалити</x-catalog.button>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="px-4 py-10 text-center text-slate-700">Надлишків матеріалів поки немає.</td>
            </tr>
        @endforelse
    </x-catalog.table>

    <x-modal name="confirm-material-surplus-delete" maxWidth="md" focusable>
        <div class="p-6" role="dialog" aria-modal="true" aria-labelledby="material-surplus-delete-title">
            <h3 id="material-surplus-delete-title" class="text-xl font-bold text-[#174a47]">Видалити документ надлишків № <span x-text="pendingDocument"></span>?</h3>
            <p class="mt-3 text-sm leading-relaxed text-gray-600">Документ надлишків буде видалено. Цю дію неможливо скасувати.</p>
            <p x-show="deleteError" x-text="deleteError" role="alert" class="mt-3 text-sm text-red-600"></p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" :disabled="deleting" class="catalog-button catalog-button-secondary disabled:opacity-50">Скасувати</button>
                <button type="button" :disabled="deleting || !pendingDocument" x-on:click="deleting = true; deleteError = ''; try { await $wire.deleteDocument(pendingDocument); $dispatch('close-modal', 'confirm-material-surplus-delete'); } catch (error) { deleteError = 'Не вдалося видалити документ. Оновіть сторінку та перевірте його стан.'; } finally { deleting = false; }" class="catalog-button catalog-button-danger disabled:cursor-wait"><span x-text="deleting ? 'Видалення…' : 'Видалити документ'"></span></button>
            </div>
        </div>
    </x-modal>

    <x-modal name="create-material-surplus" maxWidth="2xl" focusable>
        <div role="dialog" aria-modal="true" aria-labelledby="create-material-surplus-title">
            <div class="flex items-start justify-between gap-4 border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4 sm:px-6">
                <div>
                    <h3 id="create-material-surplus-title" class="text-xl font-bold text-[#174a47]">{{ $editingId ? 'Редагувати надлишки №'.$editingId : 'Додати надлишки матеріалу' }}</h3>
                    <p class="mt-1 text-sm text-slate-600">Оберіть матеріал за артикулом і вкажіть кількість та цех.</p>
                </div>
                <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити" class="rounded-md p-2 text-[#245b53] hover:bg-white">
                    <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="space-y-5 p-5 sm:p-6">
                <div>
                    <label for="surplus-material-article" class="mb-2 block text-sm font-semibold text-slate-800">Артикул матеріалу</label>
                    <input id="surplus-material-article" type="search" wire:model.live.debounce.250ms="articleSearch" autocomplete="off"
                        placeholder="Введіть артикул матеріалу"
                        class="block h-11 w-full rounded-md border-gray-300 text-base shadow-sm focus:border-teal-600 focus:ring-teal-600" />
                    @error('selectedMaterialId') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    @if($selectedMaterial)
                        <div class="mt-2 rounded-md border border-[#a8c8c5] bg-[#f2f7f6] px-3 py-2 text-sm text-slate-800">
                            <strong>{{ $selectedMaterial->article }}</strong> — {{ $selectedMaterial->name }}
                            <span class="text-slate-600">({{ $selectedMaterial->unit?->unit ?? '—' }})</span>
                        </div>
                    @elseif($materialOptions->isNotEmpty())
                        <ul class="mt-1 max-h-52 overflow-y-auto rounded-md border border-gray-200 bg-white shadow-sm">
                            @foreach($materialOptions as $materialOption)
                                <li wire:key="surplus-material-option-{{ $materialOption->id }}">
                                    <button type="button" wire:click="selectMaterial({{ $materialOption->id }})" class="w-full px-3 py-2 text-left hover:bg-[#e3f1ee]">
                                        <span class="font-semibold">{{ $materialOption->article }}</span> — {{ $materialOption->name }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @elseif(mb_strlen(trim($articleSearch)) >= 1)
                        <p class="mt-1 text-sm text-slate-600">Матеріалів із таким артикулом не знайдено.</p>
                    @endif
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="surplus-quantity" class="mb-2 block text-sm font-semibold text-slate-800">Кількість надлишків</label>
                        <input id="surplus-quantity" type="number" min="0.001" step="0.001" wire:model="quantity" required
                            class="block h-11 w-full rounded-md border-gray-300 text-base shadow-sm focus:border-teal-600 focus:ring-teal-600" />
                        @error('quantity') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="surplus-department" class="mb-2 block text-sm font-semibold text-slate-800">Цех, до якого зараховується</label>
                        <select id="surplus-department" wire:model="departmentId" required
                            class="block h-11 w-full rounded-md border-gray-300 text-base shadow-sm focus:border-teal-600 focus:ring-teal-600">
                            <option value="">Оберіть цех</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->number }}</option>
                            @endforeach
                        </select>
                        @error('departmentId') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-[#d4e5e0] pt-4">
                    <x-catalog.button type="button" x-on:click="$dispatch('close')">Скасувати</x-catalog.button>
                    <x-catalog.button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">
                        Зберегти надлишки
                    </x-catalog.button>
                </div>
            </form>
        </div>
    </x-modal>
            </div>
        </div>
    </div>
</div>
