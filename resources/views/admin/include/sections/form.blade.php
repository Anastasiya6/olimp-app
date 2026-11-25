@php
    $editing = isset($section);
    $formPrefix = $editing ? 'edit-section' : 'create-section';
    $restoreInput = !($inModal ?? false) || ($editing ? (string) old('_section_edit') === (string) $section->id : (bool) old('_section_create'));
    $departmentId = $section->department_id ?? null;
    $selectedDepartment = $restoreInput ? old('department_id', $departmentId) : $departmentId;
    $chiefId = $section->chief_user_id ?? null;
    $selectedChief = $restoreInput ? old('chief_user_id', $chiefId) : $chiefId;
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
        <div class="sm:col-span-2">
            <label for="{{ $formPrefix }}-number" class="catalog-label">Номер</label>
            <input id="{{ $formPrefix }}-number" type="text" name="number"
                value="{{ $restoreInput ? old('number', $section->number ?? '') : ($section->number ?? '') }}"
                class="catalog-input" minlength="2" />
            @if($restoreInput)
                @error('number') <p class="mt-2 text-base text-red-700">{{ $message }}</p> @enderror
            @endif
        </div>
        <div class="sm:col-span-2">
            <label for="{{ $formPrefix }}-chief" class="catalog-label">Начальник ділянки</label>
            <select id="{{ $formPrefix }}-chief" name="chief_user_id" class="catalog-input" required>
                <option value="" disabled @selected(!$selectedChief)>Виберіть користувача</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected((string) $selectedChief === (string) $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
            @if($restoreInput)
                @error('chief_user_id') <p class="mt-2 text-base text-red-700">{{ $message }}</p> @enderror
            @endif
        </div>
        <div class="sm:col-span-2">
            <label for="{{ $formPrefix }}-department" class="catalog-label">Цех</label>
            <select id="{{ $formPrefix }}-department" name="department_id" class="catalog-input" required>
                <option value="" disabled @selected($selectedDepartment === null || $selectedDepartment === '')>Виберіть цех</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" @selected((string) $selectedDepartment === (string) $department->id)>{{ $department->number }}</option>
                @endforeach
            </select>
            @if($restoreInput)
                @error('department_id') <p class="mt-2 text-base text-red-700">{{ $message }}</p> @enderror
            @endif
        </div>
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
