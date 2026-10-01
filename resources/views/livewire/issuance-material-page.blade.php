<div class="issuance-document-page" x-data="{}">

    {{-- HEADER --}}
    @unless($inModal)
    <x-slot name="header" compact="true">
        <h2 class="text-xl font-bold leading-tight text-[#174a47]">
            @if($isEdit)
                Редагування документа №{{ $materialIssuanceId }}
            @else
                Створення видачі матеріалів
            @endif
        </h2>
    </x-slot>
    @endunless

    <div class="bg-[#f2f7f6] py-3 sm:py-4">

        <div class="mx-auto max-w-7xl px-3 sm:px-6 lg:px-8">

            <div class="rounded-lg border border-[#bfd8d1] bg-white p-4 shadow-sm sm:p-5">
                <div class="flex justify-end mb-4">
                    <x-catalog.button variant="secondary"
                        type="button"
                        x-on:click="$dispatch('open-modal', 'confirm-close-issuance')"

                    >
                        Закрити
                    </x-catalog.button>
                </div>
                {{-- FORM --}}
                <div class="mb-5 space-y-4">

                    {{-- 🔹 ПЕРШИЙ РЯДОК (ПІБ) --}}
                    <div class="grid grid-cols-12 gap-4">

                        {{-- Хто отримує --}}
                        <div class="col-span-12 sm:col-span-6">
                            <label class="block">
                                <span class="text-[#245b53] font-medium">Хто отримує матеріал</span>
                                <select
                                    wire:model="received_by_user_id"
                                    @disabled($isEdit)
                                    class="block w-full mt-1 rounded-md
                                        @error('received_by_user_id')
                                            border border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500
                                        @else
                                            border border-gray-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600
                                        @enderror
                                        ">
                                    <option value="">Оберіть співробітника</option>

                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('received_by_user_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </label>
                        </div>

                        {{-- Хто виписує --}}
                        <div class="col-span-12 sm:col-span-6">
                            <label class="block">
                                <span class="text-[#245b53] font-medium">Хто виписує документ</span>
                                <select
                                    wire:model="issued_by_user_id"
                                    @disabled($isEdit)
                                    class="block w-full mt-1 rounded-md
                                    @error('issued_by_user_id')
                                        border border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500
                                    @else
                                        border border-gray-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600
                                    @enderror
                                        ">
                                    <option value="">Оберіть співробітника</option>

                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('issued_by_user_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </label>
                        </div>

                    </div>

                    {{-- 🔹 ДРУГИЙ РЯДОК (замовлення + деталь + кількість + кнопка) --}}
                    <div class="grid grid-cols-12 gap-4">

                        {{-- ORDER --}}
                        <div class="col-span-12 sm:col-span-6 lg:col-span-3">
                            <label class="block">
                                <span class="text-[#245b53] font-medium">Замовлення</span>
                                <select
                                    wire:model="order_name_id" wire:change="updateSearch"
                                    @disabled($isEdit)
                                    class="block w-full mt-1 rounded-md
                                    @error('order_name_id')
                                        border border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500
                                    @else
                                        border border-gray-300 focus:border-teal-600 focus:ring-1 focus:ring-teal-600
                                    @enderror
                                        ">
                                    <option value="">—</option>
                                    @foreach($order_names as $order)
                                        <option value="{{ $order->id }}">
                                            {{ $order->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('order_name_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </label>
                        </div>

                        {{-- DESIGNATION --}}
                        <div class="col-span-12 sm:col-span-6 lg:col-span-3">
                            <label class="block">
                                <span class="text-[#245b53] font-medium">Деталь</span>

                                @if($isEdit)
                                    {{-- тільки показуємо --}}
                                    <div class="mt-1 p-2 border rounded-md bg-gray-100">
                                        {{ $designationName }}
                                    </div>
                                @else
                                    {{-- вибір деталі --}}
                                    <div wire:ignore x-data="{}" x-init="$nextTick(() => window.initDesignationSelect())">
                                        <input
                                            id="designation-select"
                                            class="block w-full mt-1 border-gray-300 rounded-md"
                                        >
                                    </div>
                                @endif

                            </label>
                        </div>

                        <div class="col-span-12 sm:col-span-6 lg:col-span-3">
                            <label class="block">
                                <span class="text-[#245b53] font-medium">Деталь з плану</span>

                                @if($planDetails->isNotEmpty())

                                    @if($planDetails->count() === 1)

                                        <div class="mt-1 p-2 border rounded-md bg-gray-100">
                                            {{ $planDetails->first()->designation->designation }}
                                        </div>

                                    @else

                                        <select
                                            wire:model="selectedPlanTask"
                                            class="block w-full mt-1 border-gray-300 rounded-md"
                                        >
                                            <option value="">— Оберіть деталь —</option>

                                            @foreach($planDetails as $task)
                                                <option value="{{ $task->designation_id }}">
                                                    {{ $task->designation->designation }} — застосовність: {{ $task->quantity }}
                                                </option>
                                            @endforeach
                                        </select>

                                    @endif

                                @else

                                    @if($isEdit)
                                        <input
                                            type="text"
                                            readonly
                                            value="{{ $planDesignationName }}"
                                            class="block w-full mt-1 border-gray-300 rounded-md bg-gray-100"
                                        >
                                    @else
                                        <input
                                            type="text"
                                            readonly
                                            class="block w-full mt-1 border-gray-300 rounded-md bg-gray-100"
                                        >
                                    @endif

                                @endif
                            </label>
                        </div>
                        {{-- QUANTITY --}}
                        <div class="col-span-6 lg:col-span-1">
                            <label class="block">
                                <span class="text-[#245b53] font-medium">Кількість</span>
                                <input
                                    type="number"
                                    wire:model.live="quantity"
                                    class="block w-full mt-1 rounded-md border
                                    @error('quantity')
                                        border-red-500 focus:border-red-500 focus:ring-red-500
                                    @else
                                        border-gray-300 focus:border-teal-600 focus:ring-teal-600
                                    @enderror"
                                />

                                @error('quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </label>
                        </div>

                        {{-- BUTTON --}}
                        @if(!$isEdit)
                            <div class="col-span-6 flex items-end lg:col-span-2">
                                <x-catalog.button variant="primary"
                                    wire:click="generate"
                                    class="w-full"
                                >
                                    Сформувати
                                </x-catalog.button>
                            </div>
                        @endif

                    </div>

                </div>

                {{-- TABLE --}}
                @if($materials && !empty($materials))
                    <div class="overflow-hidden rounded-lg border border-[#a8c8c5]">
                    <div class="overflow-x-auto">
                    <table class="catalog-table">
                        <thead>
                        <tr>
                            <th scope="col">Деталь</th>
                            <th scope="col">Матеріал</th>
                            <th scope="col">Норма витрат на виріб</th>
                            <th scope="col">К-сть</th>
                            <th scope="col">Од.</th>
                            <th scope="col">Норма</th>
                            <th scope="col">Множник</th>
                            <th scope="col"></th>
                            <th scope="col">Дії</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($materials as $index => $material)
                            @php
                                if (is_numeric($material['material_id'])) {
                                    $hasTaken = array_key_exists(
                                        $material['material_id'],
                                        $selectedMaterials['material_id']
                                    );

                                    $taken = $selectedMaterials['material_id'][$material['material_id']] ?? 0;
                                } else {
                                    $hasTaken = array_key_exists(
                                        $material['designation_id'],
                                        $selectedMaterials['designation_id']
                                    );

                                    $taken = $selectedMaterials['designation_id'][$material['designation_id']] ?? 0;
                                }
                            @endphp
                            <tr class="{{ $hasTaken ? 'issuance-material-taken' : '' }}">
                                <td>{{ $material['detail'] }}</td>
                                <td>{{ $material['material'] }}</td>
                                <td>{{ $material['print_value'] / $quantity }}</td>
                                <td>{{ $quantity }}</td>
                                <td>{{ $material['unit'] }}</td>
                                <td>{{ $material['print_value']  }}</td>
                                <td>{{ $material['multiplier_str'] }}</td>
                                <td>{{ $material['multiplier'] ? $material['print_value'] * $material['multiplier'] : $material['print_value']}}</td>
                                <td>
                                    @if(!$hasTaken)
                                        <x-catalog.button variant="primary"
                                            wire:click="openModal('{{ $material['material_id'] }}',
                                                                    '{{ $material['detail'] }}',
                                                                    '{{ $material['material'] }}',odex
                                                                    '{{ $material['print_value']}}')"

                                        >
                                            Видати матеріал
                                        </x-catalog.button>
                                    @endif
                                    @if($isEdit && $hasTaken)
                                        <x-catalog.button variant="primary"
                                        wire:click="openEditModal('{{ $material['material_id']}}',
                                                                    '{{ $material['detail'] }}',
                                                                    '{{ $material['material'] }}',
                                                                    '{{ $materialIssuanceId }}')">
                                            Редагувати
                                        </x-catalog.button>
                                    @endif
                                    @if($hasTaken)
                                        <div class="text-xs text-green-600 mt-1">
                                            Видано: {{ $taken }}
                                        </div>
                                    @endif

                                    @if($hasTaken)
                                        <x-catalog.button variant="secondary"
                                            wire:click="removeMaterial('{{ $material['material_id'] }}')"
                                            class="mt-1"
                                        >
                                            Відмінити
                                        </x-catalog.button>

                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        {{-- Підключаємо модалку як окремий компонент --}}

                        </tbody>
                    </table>
                    </div>
                    </div>
                    @if($inModal)
                        @teleport('body')
                            <div><livewire:material-take-modal :materials="$materials" /></div>
                        @endteleport
                    @else
                        <livewire:material-take-modal :materials="$materials" />
                    @endif
                @else
                    <p class="text-gray-500 text-center">
                        Матеріали ще не сформовані
                    </p>
                @endif

            </div>

        </div>
    </div>

    @teleport('body')
    <x-modal name="confirm-close-issuance" maxWidth="md" focusable>
        <div class="p-6" role="dialog" aria-modal="true" aria-labelledby="close-issuance-title" aria-describedby="close-issuance-description">
            <h3 id="close-issuance-title" class="text-xl font-bold text-[#174a47]">Закрити документ?</h3>
            <p id="close-issuance-description" class="mt-3 text-sm leading-relaxed text-gray-600">Ви дійсно хочете закрити документ і повернутися до списку видачі матеріалів?</p>
            <div class="mt-6 flex justify-end gap-3">
                <x-catalog.button type="button" variant="secondary" x-on:click="$dispatch('close')">Скасувати</x-catalog.button>
                <x-catalog.button type="button" wire:click="closeDocument" wire:loading.attr="disabled" wire:target="closeDocument">Закрити документ</x-catalog.button>
            </div>
        </div>
    </x-modal>
    @endteleport
</div>
