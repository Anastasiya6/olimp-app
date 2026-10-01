@php
    $editing = isset($designation);
    $formPrefix = $editing ? 'edit-designation' : 'create-designation';
    $restoreInput = !($inModal ?? false) || ($editing ? (string) old('_designation_edit') === (string) $designation->id : (bool) old('_designation_create'));
    $fields = ['designation' => 'Номер', 'name' => 'Назва', 'route' => 'Маршрут', 'code_1c' => 'Код 1С'];
@endphp
<form method="POST" action="{{ $editing ? route('designations.update', $designation->id) : route('designations.store') }}" class="catalog-form">
    @csrf
    @if($editing)
        @method('PUT')
    @endif
    @if($inModal ?? false)
        <input type="hidden" name="{{ $editing ? '_designation_edit' : '_designation_create' }}" value="{{ $editing ? $designation->id : 1 }}" />
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
    </div>
    <div class="mt-6 flex flex-wrap justify-end gap-3 border-t border-[#d4e5e0] pt-5">
        @if($inModal ?? false)
            <x-catalog.button x-on:click="$dispatch('close')">Скасувати</x-catalog.button>
        @else
            <x-catalog.button :href="route('designations.index')">Скасувати</x-catalog.button>
        @endif
        <x-catalog.button type="submit" variant="primary">{{ $editing ? 'Зберегти зміни' : 'Зберегти виріб' }}</x-catalog.button>
    </div>
</form>
