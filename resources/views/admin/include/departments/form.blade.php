@php
    $editing = isset($department);
    $formPrefix = $editing ? 'edit-department' : 'create-department';
    $restoreInput = !($inModal ?? false) || ($editing ? (string) old('_department_edit') === (string) $department->id : (bool) old('_department_create'));
    $fields = ['number' => 'Цех'];
@endphp
<form method="POST" action="{{ $editing ? route('departments.update', $department->id) : route('departments.store') }}" class="catalog-form">
    @csrf
    @if($editing)
        @method('PUT')
    @endif
    @if($inModal ?? false)
        <input type="hidden" name="{{ $editing ? '_department_edit' : '_department_create' }}" value="{{ $editing ? $department->id : 1 }}" />
    @endif
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        @foreach($fields as $field => $label)
            <div class="{{ $field === 'number' ? 'sm:col-span-2' : '' }}">
                <label for="{{ $formPrefix }}-{{ $field }}" class="catalog-label">{{ $label }}</label>
                <input id="{{ $formPrefix }}-{{ $field }}" type="text" name="{{ $field }}" maxlength="2" minlength="2"
                    value="{{ $restoreInput ? old($field, $editing ? $department->{$field} : '') : ($editing ? $department->{$field} : '') }}"
                    class="catalog-input" @required($field === 'number') />
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
            <x-catalog.button :href="route('departments.index')">Скасувати</x-catalog.button>
        @endif
        <x-catalog.button type="submit" variant="primary">{{ $editing ? 'Зберегти зміни' : 'Зберегти цех' }}</x-catalog.button>
    </div>
</form>
