@php
    $editing = isset($designation);
    $formPrefix = $editing ? 'edit-pi0' : 'create-pi0';
    $restoreInput = !($inModal ?? false) || ($editing ? (string) old('_pi0_edit') === (string) $designation->id : (bool) old('_pi0_create'));
    $fields = ['designation' => 'Номер', 'name' => 'Назва', 'gost' => 'ГОСТ', 'code_1c' => 'Код 1С'];
@endphp
<form method="POST" action="{{ $editing ? route('pi0s.update', $designation->id) : route('pi0s.store') }}" class="catalog-form">
    @csrf
    @if($editing)
        @method('PUT')
    @endif
    @if($inModal ?? false)
        <input type="hidden" name="{{ $editing ? '_pi0_edit' : '_pi0_create' }}" value="{{ $editing ? $designation->id : 1 }}" />
    @endif
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        @foreach($fields as $field => $label)
            <div class="{{ in_array($field, ['designation', 'name']) ? 'sm:col-span-2' : '' }}">
                <label for="{{ $formPrefix }}-{{ $field }}" class="catalog-label">{{ $label }}</label>
                <input id="{{ $formPrefix }}-{{ $field }}" type="text" name="{{ $field }}"
                    value="{{ $restoreInput ? old($field, $editing ? $designation->{$field} : '') : ($editing ? $designation->{$field} : '') }}"
                    class="catalog-input" @required($field === 'designation') />
                @if($restoreInput)
                    @error($field) <p class="mt-2 text-base text-red-700">{{ $message }}</p> @enderror
                @endif
            </div>
        @endforeach
        <div>
            <label for="{{ $formPrefix }}-unit" class="catalog-label">Одиниця виміру</label>
            @php
                $selectedUnit = $editing ? $designation->type_unit_id : $units->first()?->id;
                $selectedUnit = $restoreInput ? old('type_unit_id', $selectedUnit) : $selectedUnit;
            @endphp
            <select id="{{ $formPrefix }}-unit" name="type_unit_id" class="catalog-input">
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" @selected((string) $selectedUnit === (string) $unit->id)>{{ $unit->unit }}</option>
                @endforeach
            </select>
            @if($restoreInput)
                @error('type_unit_id') <p class="mt-2 text-base text-red-700">{{ $message }}</p> @enderror
            @endif
        </div>
    </div>
    <div class="mt-6 flex flex-wrap justify-end gap-3 border-t border-[#d4e5e0] pt-5">
        @if($inModal ?? false)
            <x-catalog.button x-on:click="$dispatch('close')">Скасувати</x-catalog.button>
        @else
            <x-catalog.button :href="route('pi0s.index')">Скасувати</x-catalog.button>
        @endif
        <x-catalog.button type="submit" variant="primary">{{ $editing ? 'Зберегти зміни' : 'Зберегти ПИ0' }}</x-catalog.button>
    </div>
</form>
