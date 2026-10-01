@php
    $editing = isset($specification);
    $prefix = $editing ? 'edit-specification' : 'create-specification';
    $restoreInput = !($inModal ?? false) || ($editing ? (string) old('_specification_edit') === (string) $specification->id : (bool) old('_specification_create'));
@endphp
<form method="POST" action="{{ $editing ? route('specifications.update', $specification->id) : route('specifications.store') }}" class="catalog-form">
    @csrf
    @if($editing)
        @method('PUT')
    @endif
    @if($inModal ?? false)
        <input type="hidden" name="{{ $editing ? '_specification_edit' : '_specification_create' }}" value="{{ $editing ? $specification->id : 1 }}" />
    @endif
    @if($editing)
        @foreach(['designation_designation' => ['Куди', $specification->designations->designation ?? ''], 'designation_entry_designation' => ['Що', $specification->designationEntry->designation ?? '']] as $field => [$label, $value])
            <div class="mb-5">
                <label for="{{ $prefix }}-{{ $field }}" class="catalog-label">{{ $label }}</label>
                <input id="{{ $prefix }}-{{ $field }}" type="text" name="{{ $field }}" readonly value="{{ $value }}" class="catalog-input" />
            </div>
        @endforeach
    @else
        <livewire:specification-designation-search key="create-specification-parent" />
        <livewire:specification-search-dropdown key="create-specification-entry" />
    @endif
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        @foreach(['specification_quantity' => ['Кількість', $editing ? $specification->quantity : ''], 'specification_category_code' => ['Шифр', $editing ? $specification->category_code : '']] as $field => [$label, $value])
            <div>
                <label for="{{ $prefix }}-{{ $field }}" class="catalog-label">{{ $label }}</label>
                <input id="{{ $prefix }}-{{ $field }}" type="text" name="{{ $field }}" value="{{ $restoreInput ? old($field, $value) : $value }}" class="catalog-input" />
            </div>
        @endforeach
    </div>
    @if($restoreInput && $errors->any())
        <ul role="alert" class="mt-4 list-inside list-disc p-3 text-sm text-red-700">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    @endif
    <div class="mt-6 flex flex-wrap justify-end gap-3 border-t border-[#d4e5e0] pt-5">
        @if($inModal ?? false)
            <x-catalog.button x-on:click="$dispatch('close')">Скасувати</x-catalog.button>
        @else
            <x-catalog.button :href="route('specifications.index')">Скасувати</x-catalog.button>
        @endif
        <x-catalog.button type="submit" variant="primary">{{ $editing ? 'Зберегти зміни' : 'Зберегти запис' }}</x-catalog.button>
    </div>
</form>
