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
                                <span class="text-gray-700">Деталь</span>
                                <input type="text" name="designation"
                                       class="block w-full mt-1 rounded-md" readonly
                                       placeholder="" value="{{old('designation',$item->designation->designation)}}" />
                            </label>
                            @error('designation')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Документ</span>
                                <input type="text" name="document_number"
                                       class="block w-full mt-1 rounded-md" readonly
                                       placeholder="" value="{{old('document_number',$item->document_number)}}" />
                            </label>
                            @error('document_number')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Замовлення</span>
                                <input type="text" name="order_number"
                                       class="block w-full mt-1 rounded-md" readonly
                                       placeholder="" value="{{old('designation',$item->order_number)}}" />
                            </label>
                            @error('order_number')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Кількість</span>
                                <input type="text" name="quantity"
                                       class="block w-full mt-1 rounded-md"
                                       placeholder="" value="{{old('quantity',$item->quantity)}}" />
                            </label>
                            @error('quantity')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Виберіть цех</span>
                                <select name="department_id" class="block w-full mt-1 rounded-md">
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}"
                                                @if($item->department_id == $department->id) selected @endif 'selected' }}>
                                        {{ $department->number }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                            @error('department_id')
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
