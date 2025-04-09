<div>
    <div class="flex flex-wrap -mx-3 mb-6">
        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
            <label for="search" class="sr-only">Search for details</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 mt-6 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                              clip-rule="evenodd" />
                    </svg>
                </div>
                <label class="block">
                    <span class="text-gray-700">Виберіть деталь</span>
                    <input wire:model="search"
                           wire:keyup="searchResult"
                           autocomplete="off"
                           id="search" autofocus
                           class="block w-full py-3 px-4 pl-10 border bg-gray-200 border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-blue-300 focus:shadow-outline-blue sm:text-sm transition duration-150 ease-in-out"
                           placeholder="Пошук деталі..." type="search" autocomplete="off">
                </label>
                @if (strlen($search) > 1)
                    <ul class="absolute z-50 bg-white border-gray-300 w-full rounded-md mt-2 text-gray-700 text-sm divide-y divide-gray-200">
                        @forelse ($searchResults as $result)
                            <li>
                                <a href="#" wire:click.prevent="selectSearch('{{ $result->id }}', '{{ $result->designation }}', '{{ addslashes($result->name) }}')"
                                   wire:key="{{ $result->id }}"
                                   class="flex items-center px-4 py-4 hover:bg-gray-200 transition ease-in-out duration-150">
                                    <div class="ml-4 leading-tight">
                                        <div class="font-semibold">{{ $result->designation }}</div>
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
        <input type="hidden" name="{{$designation_hidden}}" value="{{ $selectedDesignationId }}">
        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
            <label class="block">
                <span class="text-gray-700">{{$designation_title}}</span>
                <input type="text" readonly name="{{$designation_name}}" class="block w-full py-3 px-4 border bg-gray-200 border-gray-300 rounded-md leading-5 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-blue-300 focus:shadow-outline-blue sm:text-sm transition duration-150 ease-in-out" placeholder=""
                       value="{{$selectedDesignation}}" />
            </label>
            @error('designation')
            <div class="text-sm text-red-600">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

