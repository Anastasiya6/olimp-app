<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Створення видачі матеріалів
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 space-y-6">

            {{-- Дані документа --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="grid grid-cols-12 gap-6">

                    <div class="col-span-6">
                        <label class="block">
                            <span class="text-gray-700">Хто отримує матеріал</span>

                            <select
                                wire:model="received_by_user_id"
                                class="mt-1 block w-full rounded-md border-gray-300"
                            >
                                <option value="">Оберіть співробітника</option>

                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    <div class="col-span-6">
                        <label class="block">
                            <span class="text-gray-700">Хто виписує документ</span>

                            <select
                                wire:model="issued_by_user_id"
                                class="mt-1 block w-full rounded-md border-gray-300"
                            >
                                <option value="">Оберіть співробітника</option>

                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                </div>
            </div>

            {{-- Матеріал --}}
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-5">

                <livewire:import-material-stock-search-dropdown
                    :material_id="$selectedMaterialId"
                    :material_name="$selectedMaterial"
                />

                <div>
                    <label class="block">
                        <span class="text-sm text-gray-700">Кількість</span>

                        <input
                            type="number"
                            step="0.01"
                            wire:model="quantity"
                            class="mt-1 block w-full rounded-md border-gray-300"
                        >
                    </label>
                </div>

                <div class="flex justify-end">
                    <x-primary-button wire:click="save">
                        Зберегти
                    </x-primary-button>
                </div>

            </div>

        </div>
    </div>
</div>
