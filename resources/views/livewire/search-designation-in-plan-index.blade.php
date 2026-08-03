<div>

    {{-- HEADER --}}
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Пошук деталі в плані (по вибраному замовленню)
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form wire:submit="search">
                    <div class="gap-4 sm:flex py-6">

                        <input
                            class="block rounded-md"
                            type="text"
                            wire:model="designation_number"
                            placeholder="Вузол"
                        />

                        <label>Замовлення</label>

                        <select
                            wire:model="selectedOrder"
                            class="block rounded-md"
                            style="width:150px"
                        >
                            @foreach($order_names as $order_name)
                                <option value="{{ $order_name->id }}">
                                    {{ $order_name->name }}
                                </option>
                            @endforeach
                        </select>

                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded"
                        >
                            Шукати
                        </button>

                    </div>
                </form>
                {{-- ТАБЛИЦЯ --}}
                <table class="w-full border">
                    <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border">Деталь або вузол в плані</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($results as $item)
                        <tr>
                            <td class="border p-2">
                                {{ $item->designation->designation }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center p-3">
                                Нічого не знайдено
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                {{-- PAGINATION --}}
{{--                <div class="mt-4">--}}
{{--                    {{ $items->links() }}--}}
{{--                </div>--}}

            </div>

        </div>
    </div>
</div>

