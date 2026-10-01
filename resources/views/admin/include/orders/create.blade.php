<x-catalog.page title="Додати розузловання" width="max-w-5xl">
    <x-catalog.panel>
<form class="catalog-form" method="POST" action="{{ route($route.'.store') }}">
                        @csrf
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Виберіть замовлення</span>
                                <select name="order_name_id" class="catalog-input">
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
                                       class="catalog-input"
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
                                       class="catalog-input"
                                       placeholder="" value="{{old('quantity')}}" />
                            </label>
                            @error('quantity')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <x-catalog.button variant="primary" type="submit">
                            Зберегти
                        </x-catalog.button>

                    </form>
    </x-catalog.panel>
</x-catalog.page>
