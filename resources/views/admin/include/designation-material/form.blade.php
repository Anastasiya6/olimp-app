@php
    $editing = isset($designationMaterial);
    $formPrefix = $editing ? 'edit-norm' : 'create-norm';
    $restoreInput = !($inModal ?? false) || ($editing ? (string) old('_norm_edit') === (string) $designationMaterial->id : (bool) old('_norm_create'));
    $formOld = fn ($key, $default = null) => $restoreInput ? old($key, $default) : $default;
@endphp
<form method="POST" action="{{ $editing ? route($route.'.update', $designationMaterial->id) : route($route.'.store') }}" class="catalog-form">
    @csrf
    @if($inModal ?? false)
        <input type="hidden" name="{{ $editing ? '_norm_edit' : '_norm_create' }}" value="{{ $editing ? $designationMaterial->id : 1 }}" />
    @endif
    @if($editing)
        @method('put')
        <div class="mb-6">
            <label class="block">
                <span>Деталь</span>
                <input type="text" name="designation" readonly class="catalog-input" value="{{ $designationMaterial->designation->designation }}" />
            </label>
            @error('designation') <p class="mt-2 text-base text-red-700">{{ $message }}</p> @enderror
        </div>
        <div class="purchase-designation-fields">
        <livewire:material-search-dropdown :material_id="$designationMaterial->material_id" :material_name="$designationMaterial->material->name" :restore_input="$restoreInput && session()->hasOldInput()" :key="'norm-material-'.$designationMaterial->id.'-'.($editFormVersion ?? 0)" />
        </div>
    @else
        <div class="purchase-designation-fields">
        <livewire:designation-search-dropdown :designation_hidden="'designation_id'" :designation_title="'Деталь'" :designation_name="'designation'" last_record="App\Models\DesignationMaterial" :restore_input="$restoreInput && session()->hasOldInput()" key="create-norm-designation" />
        <livewire:material-search-dropdown :material_id="null" :material_name="null" last_record="App\Models\DesignationMaterial" :restore_input="$restoreInput && session()->hasOldInput()" key="create-norm-material" />
        </div>
    @endif
    <div class="mb-6 grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
            <label for="{{ $formPrefix }}-value" class="catalog-label">Норма</label>
            <input id="{{ $formPrefix }}-value" type="text" name="norm" value="{{ $formOld('norm', $editing ? $designationMaterial->norm : '') }}" class="catalog-input" />
            @error('norm') <p class="mt-2 text-base text-red-700">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="{{ $formPrefix }}-department" class="catalog-label">Цех</label>
            <select id="{{ $formPrefix }}-department" name="department_id" class="catalog-input">
                @foreach($departments as $department)
                    <option value="{{ $department->id }}"
                        @selected($formOld('department_id') !== null ? (string) $formOld('department_id') === (string) $department->id : ($editing ? $designationMaterial->department_id == $department->id : $department->number == $default_department))>{{ $department->number }}</option>
                @endforeach
            </select>
            @error('department_id') <p class="mt-2 text-base text-red-700">{{ $message }}</p> @enderror
        </div>
    </div>
    <div class="flex flex-wrap justify-end gap-3 border-t border-[#d4e5e0] pt-5">
        @if($inModal ?? false)
            <x-catalog.button x-on:click="$dispatch('close')">Скасувати</x-catalog.button>
        @else
            <x-catalog.button :href="route($route.'.index')">Скасувати</x-catalog.button>
        @endif
        <x-catalog.button type="submit" variant="primary">{{ $editing ? 'Зберегти зміни' : 'Зберегти норму' }}</x-catalog.button>
    </div>
</form>
