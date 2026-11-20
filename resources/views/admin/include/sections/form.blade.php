@php
    $editing = isset($section);
    $formPrefix = $editing ? 'edit-section' : 'create-section';
    $restoreInput = !($inModal ?? false) || ($editing ? (string) old('_section_edit') === (string) $section->id : (bool) old('_section_create'));
    $fields = ['number' => 'Номер', 'chief_last_name' => 'Прізвище начальника ділянки'];
@endphp
<form method="POST" action="{{ $editing ? route('sections.update', $section->id) : route('sections.store') }}" class="catalog-form">
    @csrf
    @if($editing)
        @method('PUT')
    @endif
    @if($inModal ?? false)
        <input type="hidden" name="{{ $editing ? '_section_edit' : '_section_create' }}" value="{{ $editing ? $section->id : 1 }}" />
    @endif
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        @foreach($fields as $field => $label)
            <div class="sm:col-span-2">
                <label for="{{ $formPrefix }}-{{ $field }}" class="catalog-label">{{ $label }}</label>
                <input id="{{ $formPrefix }}-{{ $field }}" type="text" name="{{ $field }}"
                    value="{{ $restoreInput ? old($field, $editing ? $section->{$field} : '') : ($editing ? $section->{$field} : '') }}"
                    class="catalog-input" @if($field === 'number') minlength="2" @endif />
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
            <x-catalog.button :href="route('sections.index')">Скасувати</x-catalog.button>
        @endif
        <x-catalog.button type="submit" variant="primary">{{ $editing ? 'Зберегти зміни' : 'Зберегти' }}</x-catalog.button>
    </div>
</form>
