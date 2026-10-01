<form method="POST" action="{{ route('materials.store') }}" class="space-y-5">
    @csrf
    <input type="hidden" name="_material_create" value="1" />
    <div>
        <label for="create-material-name" class="mb-2 block text-base font-semibold text-slate-800">Назва матеріалу</label>
        <input id="create-material-name" type="text" name="name" value="{{ old('name') }}"
            placeholder="Введіть назву матеріалу"
            class="block w-full rounded-md border-[#a8c8c5] bg-[#f7fbfa] px-3 py-3 text-lg text-slate-900 placeholder:text-slate-500 focus:border-teal-600 focus:ring-teal-600" />
        @error('name') <p class="mt-2 text-base text-red-700">{{ $message }}</p> @enderror
    </div>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
            <label for="create-material-unit" class="mb-2 block text-base font-semibold text-slate-800">Одиниця виміру</label>
            <select id="create-material-unit" name="type_unit_id"
                class="block w-full rounded-md border-[#a8c8c5] bg-[#f7fbfa] py-3 text-lg text-slate-900 focus:border-teal-600 focus:ring-teal-600">
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" @selected((string) old('type_unit_id', $units->first()?->id) === (string) $unit->id)>{{ $unit->unit }}</option>
                @endforeach
            </select>
            @error('type_unit_id') <p class="mt-2 text-base text-red-700">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="create-material-code" class="mb-2 block text-base font-semibold text-slate-800">Код 1С</label>
            <input id="create-material-code" type="text" name="code_1c" value="{{ old('code_1c') }}"
                placeholder="Введіть код"
                class="block w-full rounded-md border-[#a8c8c5] bg-[#f7fbfa] px-3 py-3 text-lg text-slate-900 placeholder:text-slate-500 focus:border-teal-600 focus:ring-teal-600" />
            @error('code_1c') <p class="mt-2 text-base text-red-700">{{ $message }}</p> @enderror
        </div>
    </div>
    <div class="flex flex-wrap justify-end gap-3 border-t border-[#d4e5e0] pt-5">
        @if($inModal ?? false)
            <button type="button" x-on:click="$dispatch('close')"
                class="inline-flex min-h-[44px] items-center rounded-md border border-[#a8c8c5] bg-white px-4 py-2 text-base font-semibold text-slate-700 hover:bg-[#edf6f3] focus:outline-none focus:ring-2 focus:ring-teal-600">Скасувати</button>
        @else
            <a href="{{ route('materials.index') }}"
                class="inline-flex min-h-[44px] items-center rounded-md border border-[#a8c8c5] bg-white px-4 py-2 text-base font-semibold text-slate-700 hover:bg-[#edf6f3] focus:outline-none focus:ring-2 focus:ring-teal-600">Скасувати</a>
        @endif
        <button type="submit"
            class="inline-flex min-h-[44px] items-center rounded-md border border-[#286357] bg-[#286357] px-5 py-2 text-base font-semibold text-white shadow-sm hover:bg-[#1e5046] focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">Зберегти матеріал</button>
    </div>
</form>
