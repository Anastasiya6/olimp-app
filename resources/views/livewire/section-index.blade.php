<div x-data="{}" x-on:section-edit-open.window="$dispatch('open-modal', 'edit-section')">
    <x-catalog.panel class="mb-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-end">
            <x-catalog.button x-on:click="$dispatch('open-modal', 'create-section')" aria-haspopup="dialog" variant="primary" class="catalog-add-button shrink-0 lg:mb-1">
                <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                </svg>
                Додати ділянку
            </x-catalog.button>
        </div>
    </x-catalog.panel>
    <x-catalog.table :items="$items">
        <x-slot:head>
            <tr>
                <th scope="col">Номер</th>
                <th scope="col">Прізвище начальника ділянки</th>
                <th scope="col">Дії</th>
            </tr>
        </x-slot:head>
        @forelse($items as $item)
            <tr wire:key="section-row-{{ $item->id }}">
                <td class="break-words">{{ $item->number ?? '—' }}</td>
                <td class="break-words">{{ $item->chief_last_name ?? '—' }}</td>
                <td>
                    <x-catalog.button wire:click="editSection({{ $item->id }})" wire:loading.attr="disabled" wire:target="editSection" aria-haspopup="dialog">Редагувати</x-catalog.button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="py-10 text-center text-base font-normal text-slate-700">
                    Записів поки немає.
                </td>
            </tr>
        @endforelse
    </x-catalog.table>
    @foreach(['create' => 'Додати ділянку', 'edit' => 'Редагувати ділянку'] as $mode => $modalTitle)
        <div wire:key="{{ $mode }}-section-dialog">
            <x-modal :name="$mode.'-section'" maxWidth="2xl" :show="(bool) old('_section_'.$mode) && $errors->any()" focusable>
                <div role="dialog" aria-modal="true" aria-labelledby="{{ $mode }}-section-title">
                    <div class="flex items-start justify-between gap-4 border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4 sm:px-6">
                        <div>
                            <h3 id="{{ $mode }}-section-title" class="text-xl font-bold text-[#174a47]">{{ $modalTitle }}</h3>
                            <p class="mt-1 text-base text-slate-600">{{ $mode === 'create' ? 'Заповніть дані ділянки.' : 'Змініть дані та збережіть зміни.' }}</p>
                        </div>
                        <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити форму ділянки"
                            class="rounded-md p-2 text-[#245b53] hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-600">
                            <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    @if($mode === 'create' || $editingSection)
                        <div class="p-5 sm:p-6" wire:key="{{ $mode }}-section-form-{{ $mode === 'edit' ? $editingSection->id.'-'.$editFormVersion : 'new' }}">
                            @include('admin.include.sections.form', ['inModal' => true, 'section' => $mode === 'edit' ? $editingSection : null])
                        </div>
                    @endif
                </div>
            </x-modal>
        </div>
    @endforeach
</div>
