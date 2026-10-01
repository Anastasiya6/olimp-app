<div class="manual-issuance-form">
    @unless($inModal)
    <x-slot name="header" compact="true">
        <h2 class="text-xl font-bold leading-tight text-[#174a47]">
            Видача матеріалів без замовлення
        </h2>
    </x-slot>
    @endunless

    <div class="bg-[#f2f7f6] py-3 sm:py-4">
        <div class="mx-auto max-w-5xl space-y-4 px-3 sm:px-5">

            {{-- Дані документа --}}
            <div class="rounded-lg border border-[#bfd8d1] bg-white p-4 shadow-sm">
                <div class="grid grid-cols-12 gap-4">

                    <div class="col-span-12 sm:col-span-6">
                        <label class="block">
                            <span class="text-[#245b53] font-medium">Хто отримує матеріал</span>

                            <select
                                wire:model="received_by_user_id"
                                class="mt-1 block w-full rounded-md border-slate-300 focus:border-teal-600 focus:ring-teal-600"
                            >
                                <option value="">Оберіть співробітника</option>

                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        @error('received_by_user_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="col-span-12 sm:col-span-6">
                        <label class="block">
                            <span class="text-[#245b53] font-medium">Хто виписує документ</span>

                            <select
                                wire:model="issued_by_user_id"
                                class="mt-1 block w-full rounded-md border-slate-300 focus:border-teal-600 focus:ring-teal-600"
                            >
                                <option value="">Оберіть співробітника</option>

                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        @error('issued_by_user_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                </div>
            </div>

            {{-- Матеріал --}}
            <div class="rounded-lg border border-[#bfd8d1] bg-white p-4 shadow-sm space-y-5">

                <div class="material-take-picker">
                <livewire:import-material-stock-search-dropdown
                    :material_id="$selectedMaterialId"
                    :material_name="$selectedMaterial"
                />
                </div>
                @error('selectedMaterialId')<p class="text-sm text-red-600">{{ $message }}</p>@enderror

                <div>
                    <label class="block">
                        <span class="text-sm text-[#245b53] font-medium">Кількість</span>

                        <input
                            type="number"
                            step="0.01"
                            wire:model="quantity"
                            class="mt-1 block w-full rounded-md border-slate-300 focus:border-teal-600 focus:ring-teal-600"
                        >
                    </label>
                </div>

                @error('quantity')<p class="text-sm text-red-600">{{ $message }}</p>@enderror

                <div class="flex flex-wrap justify-end gap-3 border-t border-[#d4e5e0] pt-5">
                    @if($inModal)
                        <x-catalog.button x-on:click="$dispatch('close')">Скасувати</x-catalog.button>
                    @else
                        <x-catalog.button :href="route('manual-issuance-materials.index')">Скасувати</x-catalog.button>
                    @endif
                    <x-catalog.button variant="primary" wire:click="save">
                        Зберегти
                    </x-catalog.button>
                </div>

            </div>

        </div>
    </div>
</div>
