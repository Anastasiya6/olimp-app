<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __($title) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route($route.'.update',$item->id) }}">
                        @csrf
                        @method('put')
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Що</span>
                                <input type="text" name="designation"
                                       class="block w-full mt-1 rounded-md" readonly
                                       placeholder="" value="{{old('designation',$item->designation->designation)}}" />
                            </label>
                            @error('designation_entry_designation')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Що</span>
                                <input type="text" name="designation_entry"
                                       class="block w-full mt-1 rounded-md" readonly
                                       placeholder="" value="{{old('designation_entry',$item->designationEntry->designation)}}" />
                            </label>
                            @error('designation_entry')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <livewire:material-search-dropdown :material_id="$item->material_id" :material_name="$item->material->name" :material_unit="$item->material->unit->unit" :show_unit="1"/>
                        <div class="flex flex-wrap -mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                <label class="block">
                                    <span class="text-gray-700">Кількість</span>
                                    <input type="text" name="norm"
                                           class="block w-full mt-1 rounded-md"
                                           placeholder="" value="{{old('norm',$item->norm)}}" />
                                </label>
                                @error('norm')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                <label class="block">
                                    <span class="text-gray-700">Код 1С</span>
                                    <input type="text" name="code_1c" class="block w-full mt-1 rounded-md" placeholder=""
                                           value="{{ old('code_1c', $item->code_1c) }}" />
                                </label>
                                @error('code_1c')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="flex justify-center">
                            <x-primary-button type="submit">
                                Оновити
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
