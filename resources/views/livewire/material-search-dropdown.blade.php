<div>
    <div class="flex flex-wrap -mx-3 mb-6">
        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
            <label for="search" class="sr-only">Search for materials</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 mt-6 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                              clip-rule="evenodd" />
                    </svg>
                </div>
                <label class="block">
                    <span class="text-gray-700">Виберіть матеріал</span>
                    <input wire:model="search"
                           wire:keyup="searchResult"
                           autocomplete="off"
                           id="search"
                           class="block w-full py-3 px-4 pl-10 border bg-gray-200 border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-blue-300 focus:shadow-outline-blue sm:text-sm transition duration-150 ease-in-out"
                           placeholder="Пошук матеріалу..." type="search" autocomplete="off">
                </label>
                @if (strlen($search) > 1)
                    <ul class="absolute z-50 bg-white border-gray-300 w-full rounded-md mt-2 text-gray-700 text-sm divide-y divide-gray-200 max-h-[200px]  overflow-y-auto">
                        @forelse ($searchResults as $result)
                            <li>
                                <a href="#" wire:click.prevent="selectSearch('{{ $result->id }}', '{{$result->name }}',  '{{$result->unit->unit }}')"
                                   wire:key="{{ $result->id }}"
                                   class="flex items-center px-4 py-4 hover:bg-gray-200 transition ease-in-out duration-150">
                                    <div class="ml-4 leading-tight">
                                        <div class="text-gray-600">{{ $result->name }}</div>
                                    </div>
                                </a>

                            </li>
                        @empty
                        @endforelse
                    </ul>
                @endif
            </div>
        </div>

        @if($selectedMaterialId)
            <input type="hidden" name="material_id" value="{{ $selectedMaterialId }}">
        @endif
        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
            <label class="block">
                <span class="text-gray-700">Матеріал</span>
                <input type="text" readonly name="material" class="block w-full py-3 px-4 border bg-gray-200 border-gray-300 rounded-md leading-5 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-blue-300 focus:shadow-outline-blue sm:text-sm transition duration-150 ease-in-out" placeholder=""
                       value="{{$selectedMaterial}}" />
            </label>
            @error('material')
                <div class="text-sm text-red-600">{{ $message }}</div>
            @enderror
        </div>
    </div>
    @if($showUnit)
        <div class="flex flex-wrap -mx-3 mb-6">
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0"></div>
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block">
                    <span class="text-gray-700">Од.вимір.</span>
                    <input type="text" readonly name="unit" class="block w-full py-3 px-4 border bg-gray-200 border-gray-300 rounded-md leading-5 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-blue-300 focus:shadow-outline-blue sm:text-sm transition duration-150 ease-in-out" placeholder=""
                           value="{{$selectedMaterialUnit}}" />
                </label>
                @error('unit')
                <div class="text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>
        </div>
    @endif
</div>


