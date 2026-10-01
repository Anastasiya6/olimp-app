@php
    $editing = isset($groupMaterial);
    $formPrefix = $editing ? 'edit-group-material' : 'create-group-material';
    $restoreInput = !($inModal ?? false) || ($editing ? (string) old('_group_material_edit') === (string) $groupMaterial->id : (bool) old('_group_material_create'));
@endphp
<form method="POST" action="{{ $editing ? route('group-materials.update', $groupMaterial->id) : route('group-materials.store') }}" class="catalog-form">
    @csrf
    @if($editing)
        @method('PUT')
    @endif
    @if($inModal ?? false)
        <input type="hidden" name="{{ $editing ? '_group_material_edit' : '_group_material_create' }}" value="{{ $editing ? $groupMaterial->id : 1 }}" />
    @endif
    @if($editing)
        <div class="mb-5">
            <label for="{{ $formPrefix }}-name" class="catalog-label">Матеріалокомплект</label>
            <input id="{{ $formPrefix }}-name" type="text" readonly value="{{ $groupMaterial->material->name ?? '—' }}" class="catalog-input" />
        </div>
        <div class="mb-5">
            <label for="{{ $formPrefix }}-entry" class="catalog-label">Матеріал</label>
            <input id="{{ $formPrefix }}-entry" type="text" readonly value="{{ $groupMaterial->materialEntry->name ?? '—' }}" class="catalog-input" />
        </div>
    @else
        <livewire:group-material-search-dropdown key="create-group-material-parent" />
        <livewire:group-material-entry-search-dropdown key="create-group-material-entry" />
    @endif
    <div class="mb-5">
        <label for="{{ $formPrefix }}-norm" class="catalog-label">Норма</label>
        <input id="{{ $formPrefix }}-norm" type="text" name="norm" class="catalog-input"
            value="{{ $restoreInput ? old('norm', $editing ? $groupMaterial->norm : '') : ($editing ? $groupMaterial->norm : '') }}" />
        @if($restoreInput)
            @error('norm') <p class="mt-2 text-base text-red-700">{{ $message }}</p> @enderror
        @endif
    </div>
    <div class="flex flex-wrap justify-end gap-3 border-t border-[#d4e5e0] pt-5">
        @if($inModal ?? false)
            <x-catalog.button x-on:click="$dispatch('close')">Скасувати</x-catalog.button>
        @else
            <x-catalog.button :href="route('group-materials.index')">Скасувати</x-catalog.button>
        @endif
        <x-catalog.button type="submit" variant="primary">{{ $editing ? 'Зберегти зміни' : 'Зберегти матеріалокомплект' }}</x-catalog.button>
    </div>
</form>
