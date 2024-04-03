<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Редагувати запис') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('specifications.update',$specification->id) }}">
                        @csrf
                        @method('put')

                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Що</span>
                                <input type="text" name="designation_entry_designation"
                                       class="block w-full mt-1 rounded-md"
                                       placeholder="" value="{{old('designation',$specification->designationEntry->designation)}}" />
                            </label>
                            @error('designation_entry_designation')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Куди</span>
                                <input type="text" name="designation_designation"
                                       class="block w-full mt-1 rounded-md"
                                       placeholder="" value="{{old('designation',$specification->designations->designation)}}" />
                            </label>
                            @error('designation_designation')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Кількість</span>
                                <input type="text" name="specification_quantity"
                                       class="block w-full mt-1 rounded-md"
                                       placeholder="" value="{{old('designation',$specification->quantity)}}" />
                            </label>
                            @error('specification_quantity')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Шифр</span>
                                <input type="text" name="specification_category_code"
                                       class="block w-full mt-1 rounded-md"
                                       placeholder="" value="{{old('designation',$specification->category_code)}}" />
                            </label>
                            @error('specification_category_code')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <x-primary-button type="submit">
                            Оновити
                        </x-primary-button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
