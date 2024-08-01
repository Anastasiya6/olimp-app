<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Створити запис') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route($route.'.store') }}">
                        @csrf
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Виберіть замволення</span>
                                <select name="order_name_id" class="block w-full mt-1 rounded-md">
                                    @foreach($order_names as $order_name)
                                        <option value="{{ $order_name->id }}">
                                            {{ $order_name->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                            @error('order_name_id')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Деталь</span>
                                <input type="text" name="designation"
                                       class="block w-full mt-1 rounded-md"
                                       placeholder="" value="{{old('code')}}" />
                            </label>
                            @error('designation')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Кількість</span>
                                <input type="text" name="quantity"
                                       class="block w-full mt-1 rounded-md"
                                       placeholder="" value="{{old('quantity')}}" />
                            </label>
                            @error('quantity')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <x-primary-button type="submit">
                            Зберегти
                        </x-primary-button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
