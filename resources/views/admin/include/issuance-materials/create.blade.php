<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Створити запис') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @livewire('issuance-material-search')
            </div>
        </div>
    </div>

{{--    <div class="py-12 search-box">--}}
{{--        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">--}}
{{--            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">--}}
{{--                <div class="p-6 bg-white border-b border-gray-200 max-w-lg w-full lg:max-w-xs">--}}
{{--                    <form method="POST" action="{{ route($route.'.store') }}">--}}
{{--                        @csrf--}}
{{--                        <div class="mb-6">--}}
{{--                            <label class="block">--}}
{{--                                <span class="text-gray-700">Виберіть замовлення</span>--}}
{{--                                <select name="order_name_id" class="block w-full mt-1 rounded-md">--}}
{{--                                    @foreach($order_names as $order_name)--}}
{{--                                        <option value="{{ $order_name->id }}"--}}
{{--                                        >{{ $order_name->name }}</option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </label>--}}
{{--                            @error('order_name_id')--}}
{{--                            <div class="text-sm text-red-600">{{ $message }}</div>--}}
{{--                            @enderror--}}
{{--                        </div>--}}
{{--                        <label class="block">--}}
{{--                            <span class="text-gray-700">Виберіть деталь</span>--}}

{{--                            <input--}}
{{--                                id="designation-select"--}}
{{--                                name="designation_id"--}}
{{--                                type="text"--}}
{{--                                placeholder="Пошук деталі..."--}}
{{--                                class="block w-full py-3 px-4 border border-gray-300 rounded-md"--}}
{{--                            >--}}
{{--                        </label>--}}
{{--                        <div class="mb-6">--}}
{{--                            <label class="block">--}}
{{--                                <span class="text-gray-700">Кількість</span>--}}
{{--                                <input type="text" name="quantity" class="block w-full mt-1 rounded-md" placeholder=""--}}
{{--                                       value="{{ old('quantity') }}" />--}}
{{--                            </label>--}}
{{--                            @error('quantity')--}}
{{--                            <div class="text-sm text-red-600">{{ $message }}</div>--}}
{{--                            @enderror--}}
{{--                        </div>--}}
{{--                        @if(!$generated)--}}
{{--                            <x-primary-button wire:click="generate">--}}
{{--                                Сформувати--}}
{{--                            </x-primary-button>--}}
{{--                        @endif--}}
{{--                        <input type="hidden" name="department_id" value="{{ $sender_department_id }}">--}}
{{--                        <div class="mb-6">--}}
{{--                            <label class="block">--}}
{{--                                <span class="text-gray-700">Виберіть одиницю виміру</span>--}}
{{--                                <select name="type_unit_id" class="block w-full mt-1 rounded-md">--}}
{{--                                    @foreach($units as $unit)--}}
{{--                                        <option value="{{ $unit->id }}">{{ $unit->unit }}</option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </label>--}}
{{--                            @error('type_unit_id')--}}
{{--                            <div class="text-sm text-red-600">{{ $message }}</div>--}}
{{--                            @enderror--}}
{{--                        </div>--}}
{{--                        <x-primary-button type="submit">--}}
{{--                            Зберегти--}}
{{--                        </x-primary-button>--}}

{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}


{{--    <script>--}}
{{--        document.addEventListener('DOMContentLoaded', () => {--}}
{{--            new TomSelect('#designation-select', {--}}
{{--                valueField: 'value',--}}
{{--                labelField: 'text',--}}
{{--                searchField: ['text'],--}}
{{--                maxItems: 1,--}}
{{--                create: false,--}}
{{--                preload: false,--}}
{{--                minChars: 2,--}}
{{--                load: function(query, callback) {--}}
{{--                    fetch(`/api/designations/search?q=${encodeURIComponent(query)}`)--}}
{{--                        .then(res => res.json())--}}
{{--                        .then(data => callback(data))--}}
{{--                        .catch(() => callback());--}}
{{--                }--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}
</x-app-layout>
