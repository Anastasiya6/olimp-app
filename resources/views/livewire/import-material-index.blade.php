<div>
    <x-slot name="header"><h2 class="text-xl font-bold text-[#174a47]">Матеріал 1С</h2></x-slot>
    <div class="bg-[#f2f7f6] py-4">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(session()->has('success'))
                <p role="status" class="mb-4 rounded-md bg-green-50 p-3 text-green-800">{{ session('success') }}</p>
            @endif
            <x-catalog.panel class="mb-4">
                <div class="flex flex-wrap items-center gap-3">
                    <input type="search" wire:model.live.debounce.350ms="search" aria-label="Пошук матеріалу 1С" placeholder="Назва, код або артикул" class="compact-search flex-1">
                    <x-catalog.button variant="primary" wire:click="openCreate" aria-haspopup="dialog" class="catalog-add-button ml-auto shrink-0">
                        <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                        </svg>
                        Додати матеріал
                    </x-catalog.button>
                </div>
            </x-catalog.panel>
            <x-catalog.table :items="$items">
                <x-slot:head><tr><th>Код 1С</th><th>Артикул</th><th>Назва матеріалу</th><th>Од. вимірювання</th></tr></x-slot:head>
                @forelse($items as $item)
                    <tr wire:key="import-material-{{ $item->id }}">
                        <td>{{ $item->code ?? '—' }}</td><td>{{ $item->article ?? '—' }}</td>
                        <td>{{ $item->name }}</td><td>{{ $item->unit?->unit ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-8 text-center">Матеріалів не знайдено.</td></tr>
                @endforelse
            </x-catalog.table>
        </div>
    </div>
    <x-modal name="create-import-material" maxWidth="2xl" focusable>
        <form wire:submit="save" class="space-y-4 p-6" aria-labelledby="create-import-material-title">
            <h3 id="create-import-material-title" class="text-xl font-bold text-[#174a47]">Додати матеріал 1С</h3>
            @foreach(['name' => 'Назва матеріалу', 'code' => 'Код 1С', 'article' => 'Артикул'] as $field => $label)
                <label class="block">
                    <span>{{ $label }}{{ $field === 'name' ? ' *' : '' }}</span>
                    <input type="text" wire:model="{{ $field }}" maxlength="255" @required($field === 'name') class="mt-1 block w-full rounded-md border-gray-300">
                    @error($field)<span class="text-sm text-red-600">{{ $message }}</span>@enderror
                </label>
            @endforeach
            <label class="block">
                <span>Одиниця вимірювання *</span>
                <select wire:model="type_unit_id" required class="mt-1 block w-full rounded-md border-gray-300">
                    <option value="">Оберіть одиницю вимірювання</option>
                    @foreach($units as $unit)<option value="{{ $unit->id }}">{{ $unit->unit }}</option>@endforeach
                </select>
                @error('type_unit_id')<span class="text-sm text-red-600">{{ $message }}</span>@enderror
            </label>
            <div class="flex justify-end gap-3">
                <x-catalog.button x-on:click="$dispatch('close')">Скасувати</x-catalog.button>
                <x-catalog.button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">Зберегти</x-catalog.button>
            </div>
        </form>
    </x-modal>
</div>
