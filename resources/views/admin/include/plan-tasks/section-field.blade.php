@php($selectedSection = (string) old('section_id', $item->section_id ?? ''))
<div class="mb-4">
    <label class="block">
        <span class="text-gray-700">Ділянка відправника</span>
        <select name="section_id" class="catalog-input" aria-invalid="{{ $errors->has('section_id') ? 'true' : 'false' }}">
            <option value="" @selected($selectedSection === '')>Без ділянки</option>
            @foreach($sections as $section)
                <option value="{{ $section->id }}" @selected($selectedSection === (string) $section->id)>{{ $section->number }}</option>
            @endforeach
        </select>
    </label>
    @error('section_id')
        <div class="text-sm text-red-600">{{ $message }}</div>
    @enderror
</div>
