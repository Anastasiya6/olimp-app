@php
    $editing = isset($typeUnit);
    $formPrefix = $editing ? 'edit-type-unit' : 'create-type-unit';
    $restoreInput = !($inModal ?? false) || ($editing ? (string) old('_typeUnit_edit') === (string) $typeUnit->id : (bool) old('_typeUnit_create'));
    $fields = ['unit' => 'Одиниця виміру'];
@endphp
<form method="POST" action="{{ $editing ? route('type_units.update', $typeUnit->id) : route('type_units.store') }}" class="catalog-form">
    @csrf
    @if($editing)
        @method('PUT')
    @endif
    @if($inModal ?? false)
        <input type="hidden" name="{{ $editing ? '_typeUnit_edit' : '_typeUnit_create' }}" value="{{ $editing ? $typeUnit->id : 1 }}" />
    @endif
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        @foreach($fields as $field => $label)
            <div class="sm:col-span-2">
                <label for="{{ $formPrefix }}-{{ $field }}" class="catalog-label">{{ $label }}</label>
                <input id="{{ $formPrefix }}-{{ $field }}" type="text" name="{{ $field }}"
                    value="{{ $restoreInput ? old($field, $editing ? $typeUnit->{$field} : '') : ($editing ? $typeUnit->{$field} : '') }}"
                    class="catalog-input" />
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
            <x-catalog.button :href="route('type_units.index')">Скасувати</x-catalog.button>
        @endif
        <x-catalog.button type="submit" variant="primary">{{ $editing ? 'Зберегти зміни' : 'Зберегти' }}</x-catalog.button>
    </div>
</form>
