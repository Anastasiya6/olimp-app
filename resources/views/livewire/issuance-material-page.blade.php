<div>

    {{-- HEADER --}}
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Створення видачі матеріалів
        </h2>
        <x-secondary-button
            onclick="return confirm('Ви впевнені, що хочете закрити документ?')"
            wire:click="closeDocument"
            class="bg-red-600 text-white hover:bg-red-700"
        >
            Закрити
        </x-secondary-button>
    </x-slot>

    <div class="py-12">

        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-end mb-4">
                    <x-secondary-button
                        wire:click="closeDocument"
                        wire:confirm="Ви впевнені, що хочете закрити документ?"
                        class="!bg-red-600 !text-white hover:!bg-red-700"
                    >
                        Закрити
                    </x-secondary-button>
                </div>
                {{-- FORM --}}
                <div class="mb-6 space-y-4">

                    {{-- 🔹 ПЕРШИЙ РЯДОК (ПІБ) --}}
                    <div class="grid grid-cols-12 gap-4">

                        {{-- Хто отримує --}}
                        <div class="col-span-6">
                            <label class="block">
                                <span class="text-gray-700">Хто отримує матеріал</span>
                                <input
                                    type="text"
                                    wire:model="issued_to_employee"
                                    class="block w-full mt-1 border-gray-300 rounded-md"
                                    placeholder="ПІБ співробітника"
                                >
                            </label>
                        </div>

                        {{-- Хто виписує --}}
                        <div class="col-span-6">
                            <label class="block">
                                <span class="text-gray-700">Хто виписує документ</span>
                                <input
                                    type="text"
                                    wire:model="issued_by_employee"
                                    class="block w-full mt-1 border-gray-300 rounded-md"
                                    placeholder="ПІБ співробітника"
                                >
                            </label>
                        </div>

                    </div>

                    {{-- 🔹 ДРУГИЙ РЯДОК (замовлення + деталь + кількість + кнопка) --}}
                    <div class="grid grid-cols-12 gap-4">

                        {{-- ORDER --}}
                        <div class="col-span-3">
                            <label class="block">
                                <span class="text-gray-700">Замовлення</span>
                                <select
                                    wire:model="order_name_id"
                                    class="block w-full mt-1 border-gray-300 rounded-md"
                                >
                                    <option value="">—</option>
                                    @foreach($order_names as $order)
                                        <option value="{{ $order->id }}">
                                            {{ $order->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                        </div>

                        {{-- DESIGNATION --}}
                        <div class="col-span-6" wire:ignore>
                            <label class="block">
                                <span class="text-gray-700">Деталь</span>
                                <input
                                    id="designation-select"
                                    class="block w-full mt-1 border-gray-300 rounded-md"
                                >
                            </label>
                        </div>

                        {{-- QUANTITY --}}
                        <div class="col-span-1">
                            <label class="block">
                                <span class="text-gray-700">Кількість</span>
                                <input
                                    type="number"
                                    wire:model="quantity"
                                    class="block w-full mt-1 border-gray-300 rounded-md"
                                >
                            </label>
                        </div>

                        {{-- BUTTON --}}
                        <div class="col-span-2 flex items-end">
                            <x-primary-button
                                wire:click="generate"
                                class="w-full bg-black hover:bg-gray-800"

                            >
                                Сформувати
                            </x-primary-button>
                        </div>

                    </div>

                </div>

                {{-- TABLE --}}
                @if($materials && !empty($materials))
                    <table class="w-full border">
                        <thead>
                        <tr class="bg-gray-100">
                            <th class="p-2 border">Деталь</th>
                            <th class="p-2 border">Матеріал</th>
                            <th class="p-2 border">Норма</th>
                            <th class="p-2 border">К-сть</th>
                            <th class="p-2 border">Од.</th>
                            <th class="p-2 border">Множник</th>
                            <th class="p-2 border">Друк</th>
                            <th class="p-2 border">Дія</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($materials as $index => $material)
                            @php
                                $taken = $selectedMaterials[$material['material_id']] ?? 0;
                            @endphp

                            <tr class="{{ $taken ? 'bg-green-50' : '' }}">
                                <td class="p-2 border">{{ $material['detail'] }}</td>
                                <td class="p-2 border">{{ $material['material'] }}</td>
                                <td class="p-2 border">{{ $material['norm'] }}</td>
                                <td class="p-2 border">{{ $material['quantity_node'] }}</td>
                                <td class="p-2 border">{{ $material['unit'] }}</td>
                                <td class="p-2 border">{{ $material['multiplier_str'] }}</td>
                                <td class="p-2 border">{{ $material['print_value'] }}</td>

                                <td class="p-2 border">
                                    <x-primary-button
                                        wire:click="openModal({{ $material['material_id'] }})"
                                        class="bg-black hover:bg-gray-800 text-white"
                                    >
                                        {{ $taken ? 'Додати ще' : 'Взяти' }}
                                    </x-primary-button>

                                    @if($taken)
                                        <div class="text-xs text-green-600 mt-1">
                                            Взято: {{ $taken }}
                                        </div>
                                    @endif

                                    @if($taken)
                                        <x-secondary-button
                                            wire:click="removeMaterial({{ $material['material_id'] }})"
                                            class="mt-1"
                                        >
                                            Відмінити
                                        </x-secondary-button>

                                        <div class="text-xs text-green-600 mt-1">
                                            Взято: {{ $taken }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        {{-- Підключаємо модалку як окремий компонент --}}
                        <livewire:material-take-modal :materials="$materials" />
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500 text-center">
                        Матеріали ще не сформовані
                    </p>
                @endif

            </div>

        </div>
    </div>

</div>
