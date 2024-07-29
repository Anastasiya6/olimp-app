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
                    <form method="POST" action="{{ route($route.'.update',$item->id) }}">
                        @csrf

                        @method('put')
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Замовлення №</span>
                                <input type="text" name="name"
                                       class="block w-full mt-1 rounded-md"
                                       placeholder="" value="{{old('name',$item->name)}}" />
                            </label>
                            @error('name')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        @include('administrator::include.order-names.checkbox')
                        <x-primary-button type="submit">
                            Оновити
                        </x-primary-button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
