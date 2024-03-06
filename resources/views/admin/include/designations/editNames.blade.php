<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Редагувати назви') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{  route('designations.update-names', ['designations_array' => $designations_array])  }}">
                        @csrf
                        @foreach($designations as $designation)

                            <div class="mb-6">
                                <label class="block">
                                    <span class="text-gray-700">Номер</span>
                                    <input type="text" name="designations[{{$designation->id}}][designation]"
                                           class="block w-full mt-1 rounded-md"
                                           placeholder="" value="{{old('designation',$designation->designation)}}" />
                                </label>
                                @error('designation')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-6">
                                <label class="block">
                                    <span class="text-gray-700">Назва</span>
                                    <input type="text" name="designations[{{$designation->id}}][name]"
                                           class="block w-full mt-1 rounded-md"
                                           placeholder="" value="{{old('name',$designation->name)}}" />
                                </label>
                                @error('name')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-6">
                                <label class="block">
                                    <span class="text-gray-700">Маршрут</span>
                                    <input type="text" name="designations[{{$designation->id}}][route]"
                                           class="block w-full mt-1 rounded-md"
                                           placeholder="" value="{{old('route',$designation->route)}}" />
                                </label>
                                @error('route')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach

                        <x-primary-button type="submit">
                            Оновити
                        </x-primary-button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
