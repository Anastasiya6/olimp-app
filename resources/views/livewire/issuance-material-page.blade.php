<div>

    {{-- HEADER --}}
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            @if($isEdit)
                Редагування документа №{{ $materialIssuanceId }}
            @else
                Створення видачі матеріалів
            @endif
        </h2>
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

                                @if($isEdit)
                                    {{-- readonly вигляд --}}
                                    <div class="mt-1 px-3 py-2 bg-gray-100 border rounded-md text-gray-500 cursor-not-allowed">
                                        {{ $issued_to_employee }}
                                    </div>
                                @else
                                    {{-- звичайний input --}}
                                    <input
                                        type="text"
                                        wire:model="issued_to_employee"
                                        class="block w-full mt-1 border-gray-300 rounded-md"
                                        placeholder="ПІБ співробітника"
                                    >
                                @endif

                            </label>
                        </div>

                        {{-- Хто виписує --}}
                        <div class="col-span-6">
                            <label class="block">
                                <span class="text-gray-700">Хто виписує документ</span>
                                <input
                                    type="text"
                                    wire:model="issued_by_employee"
                                    {{ $isEdit ? 'disabled' : '' }}
                                    class="block w-full mt-1 border-gray-300 rounded-md
                                    @if($isEdit) bg-gray-100 text-gray-400 cursor-not-allowed @endif"
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
                        <div class="col-span-6">
                            <label class="block">
                                <span class="text-gray-700">Деталь</span>

                                @if($isEdit)
                                    {{-- тільки показуємо --}}
                                    <div class="mt-1 p-2 border rounded-md bg-gray-100">
                                        {{ $designationName }}
                                    </div>
                                @else
                                    {{-- вибір деталі --}}
                                    <div wire:ignore>
                                        <input
                                            id="designation-select"
                                            class="block w-full mt-1 border-gray-300 rounded-md"
                                        >
                                    </div>
                                @endif

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
                        @if(!$isEdit)
                            <div class="col-span-2 flex items-end">
                                <x-primary-button
                                    wire:click="generate"
                                    class="w-full bg-black hover:bg-gray-800"
                                >
                                    Сформувати
                                </x-primary-button>
                            </div>
                        @endif

                    </div>

                </div>

                {{-- TABLE --}}
                @if($materials && !empty($materials))
                    <table class="w-full border">
                        <thead>
                        <tr class="bg-gray-100">
                            <th class="p-2 border">Деталь</th>
                            <th class="p-2 border">Матеріал</th>
                            <th class="p-2 border">Норма витрат на виріб</th>
                            <th class="p-2 border">К-сть</th>
                            <th class="p-2 border">Од.</th>
                            <th class="p-2 border">Норма</th>
                            <th class="p-2 border">Множник</th>
                            <th class="p-2 border"></th>
                            <th class="p-2 border">Дія</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($materials as $index => $material)
                            @php
                                $taken = is_numeric($material['material_id'])
                                 ? ($selectedMaterials['material_id'][$material['material_id']] ?? 0)
                                 : ($selectedMaterials['designation_id'][$material['designation_id']] ?? 0);
                            @endphp
                            <tr class="{{ $taken ? 'bg-green-50' : '' }}">
                                <td class="p-2 border">{{ $material['detail'] }}</td>
                                <td class="p-2 border">{{ $material['material'] }}</td>
                                <td class="p-2 border">{{ $material['print_value'] / $quantity }}</td>
                                <td class="p-2 border">{{ $quantity }}</td>
                                <td class="p-2 border">{{ $material['unit'] }}</td>
                                <td class="p-2 border">{{ $material['print_value']  }}</td>
                                <td class="p-2 border">{{ $material['multiplier_str'] }}</td>
                                <td class="p-2 border">{{ $material['multiplier'] ? $material['print_value'] * $material['multiplier'] : $material['print_value']}}</td>
                                <td class="p-2 border">
                                    @if(!$taken)
                                        <x-primary-button
                                            wire:click="openModal('{{ $material['material_id'] }}',
                                                                    '{{ $material['detail'] }}',
                                                                    '{{ $material['material'] }}',
                                                                    '{{ $materialIssuanceId }}')"
                                            class="bg-black hover:bg-gray-800 text-white"
                                        >
                                            Видати матеріал
                                        </x-primary-button>
                                    @endif
                                    @if($isEdit && $taken)
                                        <x-primary-button
                                        wire:click="openEditModal('{{ $material['material_id']}}',
                                                                    '{{ $material['detail'] }}',
                                                                    '{{ $material['material'] }}',
                                                                    '{{ $materialIssuanceId }}')">
                                            Редагувати
                                        </x-primary-button>
                                    @endif
                                    @if($taken)
                                        <div class="text-xs text-green-600 mt-1">
                                            Видано: {{ $taken }}
                                        </div>
                                    @endif

                                    @if($taken)
                                        <x-secondary-button
                                            wire:click="removeMaterial('{{ $material['material_id'] }}')"
                                            class="mt-1"
                                        >
                                            Відмінити
                                        </x-secondary-button>

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
