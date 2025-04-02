<div>
    <div class="max-w-lg w-full lg:max-w-xs">
        <label for="search" class="sr-only">Search for songs</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                          clip-rule="evenodd" />
                </svg>
            </div>
            <input wire:model="searchWhere"
                   wire:keyup="searchWhereResult"
                   autocomplete="off"
                   name="designation_designation"
                   id="search"
                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-blue-300 focus:shadow-outline-blue sm:text-sm transition duration-150 ease-in-out"
                   placeholder="Пошук для 'куди'..." type="search" autocomplete="off">
        </div>

        <div class="clear"></div>
        <div>
            @if($newDesignationWhere)
                <div class="mb-6">
                    <label class="block">
                        <span class="text-gray-700">Куди, найменування</span>
                        <input type="text" name="designation_name" class="block w-full mt-1 rounded-md" placeholder=""
                               value="" />
                    </label>
                    @error('designation_name')
                    <div class="text-sm text-red-600">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block">
                        <span class="text-gray-700">Маршрут</span>
                        <input type="text" name="designation_route" class="block w-full mt-1 rounded-md" placeholder=""
                               value="" />
                    </label>
                    @error('designation_route')
                    <div class="text-sm text-red-600">{{ $message }}</div>
                    @enderror
                </div>
            @endif
        </div>
    </div>

</div>

