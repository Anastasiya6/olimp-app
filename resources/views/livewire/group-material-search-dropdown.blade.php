<div class="mb-5">
    <label for="material_id-search" class="compact-search-label catalog-label">Матеріалокомплект</label>
    <div class="relative">
        <input id="material_id-search" type="search" wire:model="search" wire:keyup="searchResult" autocomplete="off"
            placeholder="Назва матеріалокомплекту" class="compact-search catalog-input" />
        @if(count($searchResults))
            <ul class="absolute z-50 mt-2 max-h-[200px] w-full overflow-y-auto divide-y divide-gray-200">
                @foreach($searchResults as $result)
                    <li wire:key="material_id-option-{{ $result->id }}">
                        <button type="button" wire:click="selectSearch({{ $result->id }}, {{ \Illuminate\Support\Js::from($result->name) }})"
                            class="w-full px-4 py-3 text-left hover:bg-[#e3f1ee] focus:bg-[#e3f1ee]">{{ $result->name }}</button>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
    <input type="hidden" name="material_id" value="{{ $selectedMaterialId }}" />
    <input type="text" readonly aria-label="Вибрано: Матеріалокомплект" value="{{ $selectedMaterial }}" class="catalog-input mt-2" />
    @error('material_id') <p class="mt-2 text-base text-red-700">{{ $message }}</p> @enderror
</div>
