@php
    $editing = isset($designation);
    $formPrefix = $editing ? 'edit-designation' : 'create-designation';
    $restoreInput = !($inModal ?? false) || ($editing ? (string) old('_designation_edit') === (string) $designation->id : (bool) old('_designation_create'));
    $fields = ['designation' => 'Номер', 'name' => 'Назва', 'route' => 'Маршрут', 'code_1c' => 'Код 1С'];
@endphp
<form method="POST" enctype="multipart/form-data" action="{{ $editing ? route('designations.update', $designation->id) : route('designations.store') }}" class="catalog-form">
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
    <div class="mt-5">
        <label for="{{ $formPrefix }}-drawing" class="catalog-label">Файл креслення</label>
        @if($editing && $designation->drawing_path)
            <div class="mb-2 flex flex-wrap items-center gap-3 text-sm text-slate-700">
                <span>Прикріплено: {{ $designation->drawing_original_name }}</span>
                <a href="{{ route('designations.drawing', $designation) }}" target="_blank" rel="noopener" class="font-semibold text-teal-800 underline hover:text-teal-950">Відкрити файл</a>
            </div>
        @endif
        <input id="{{ $formPrefix }}-drawing" type="file" name="drawing" class="catalog-input file:mr-3 file:rounded file:border-0 file:bg-[#e3f1ee] file:px-3 file:py-2 file:font-semibold file:text-[#174a47]" />
        <p class="mt-1 text-sm text-slate-600">Формат не обмежується, розмір — до 50 МБ. PDF та зображення відкриються у браузері; інші файли завантажаться для відкриття у відповідній програмі. Новий файл замінить прикріплений.</p>
        @if($restoreInput)
            @error('drawing') <p class="mt-2 text-base text-red-700">{{ $message }}</p> @enderror
        @endif
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
