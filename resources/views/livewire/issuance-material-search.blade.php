<div>
    <div class="grid grid-cols-12 gap-4 items-end">

        {{-- ORDER --}}
        <div class="col-span-4">
            <label class="block">
                <span class="text-gray-700">Виберіть замовлення</span>
                <select
                    wire:model="order_name_id"
                    class="block w-full mt-1 rounded-md"
                    @disabled($generated)
                >
                    <option value="">—</option>
                    @foreach($order_names as $order_name)
                        <option value="{{ $order_name->id }}">{{ $order_name->name }}</option>
                    @endforeach
                </select>
            </label>
            @error('order_name_id')
            <div class="text-sm text-red-600">{{ $message }}</div>
            @enderror
        </div>

        {{-- DESIGNATION --}}
        <div class="col-span-4" wire:ignore>
            <label class="block">
                <span class="text-gray-700">Виберіть деталь</span>
                <input
                    id="designation-select"
                    type="text"
                    class="block w-full mt-1 rounded-md"
                    @disabled($generated)
                >
            </label>
        </div>

        {{-- QUANTITY --}}
        <div class="col-span-2">
            <label class="block">
                <span class="text-gray-700">Кількість</span>
                <input
                    type="number"
                    wire:model="quantity"
                    class="block w-full mt-1 rounded-md"
                    @disabled($generated)
                >
            </label>
        </div>

        {{-- BUTTON --}}
        <div class="col-span-2">
            @if(!$generated)
                <x-primary-button
                    wire:click="generate"
                    class="w-full h-[42px]"
                >
                    Сформувати
                </x-primary-button>
            @endif
        </div>

    </div>

    {{-- TABLE --}}
    @if($materials && $materials->count())
        <table border="1" cellpadding="5">
            <thead>
            <tr>

                <th>Деталь</th>
                <th>Матеріал</th>
                <th>Норма</th>
                <th>Кількість</th>
                <th>Одиниця</th>
                <th>Множник</th>
                <th>Друковане значення</th>
            </tr>
            </thead>
            <tbody>
            @foreach($materials as $index => $material)
                @php
                    $taken = $selectedMaterials[$material['material_id']] ?? 0;
                @endphp

                <tr wire:key="row-{{ $index }}" class="{{ $taken > 0 ? 'bg-green-100' : '' }}">
                    <td>{{ $material['detail'] }}</td>
                    <td>{{ $material['material'] }}</td>
                    <td>{{ $material['norm'] }}</td>
                    <td>{{ $material['quantity_node'] }}</td>
                    <td>{{ $material['unit'] }}</td>
                    <td>{{ $material['multiplier_str'] }}</td>
                    <td>{{ $material['print_value'] }}</td>

                    <td>
                        <x-primary-button
                            wire:click="openModal('{{ $material['material_id'] }}')"
                            class="{{ $taken > 0 ? 'bg-green-500' : '' }}"
                        >
                            {{ $taken > 0 ? 'Додати ще' : 'Видати матеріал' }}
                        </x-primary-button>

                        @if($taken > 0)
                            <div class="text-green-600 text-sm">
                                Видано: {{ $taken }}
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
        <p>Матеріали ще не сформовані</p>
    @endif


</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        new TomSelect('#designation-select', {
            valueField: 'value',
            labelField: 'text',
            searchField: ['text'],
            maxItems: 1,
            create: false,
            preload: false,
            minChars: 2,
            load: function(query, callback) {
                fetch(`/api/designations/search?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => callback(data))
                    .catch(() => callback());
            },
            onChange: function(value) {
                // 🔥 Важливо: передаємо значення в Livewire
                @this.set('designation_id', value);
            }
        });
    });
</script>


