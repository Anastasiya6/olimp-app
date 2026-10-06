<div class="manual-issuance-form">
    @unless($inModal)
    <x-slot name="header" compact="true">
        <h2 class="text-xl font-bold leading-tight text-[#174a47]">
            Видача матеріалів без норм
        </h2>
    </x-slot>
    @endunless

    <div class="bg-[#f2f7f6] py-3 sm:py-4">
        <div class="mx-auto max-w-5xl space-y-4 px-3 sm:px-5">

            {{-- Дані документа --}}
            <div class="rounded-lg border border-[#bfd8d1] bg-white p-4 shadow-sm">
                <div class="grid grid-cols-12 gap-4">

                    <div class="col-span-12">
                        <label class="block">
                            <span class="text-[#245b53] font-medium">Замовлення</span>
                            <select wire:model="order_name_id" class="mt-1 block w-full rounded-md border-slate-300 focus:border-teal-600 focus:ring-teal-600">
                                <option value="">Оберіть замовлення</option>
                                @foreach($order_names as $order)
                                    <option value="{{ $order->id }}">{{ $order->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        @error('order_name_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

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

                <div class="space-y-3">
                    <label for="manual-material-search" class="block text-sm font-medium text-[#245b53]">Матеріал</label>
                    <div class="relative">
                        <input id="manual-material-search" type="search" wire:model.live.debounce.300ms="materialSearch" autocomplete="off" placeholder="Почніть вводити назву матеріалу" class="block w-full rounded-md border-slate-300 focus:border-teal-600 focus:ring-teal-600">
                        @if(strlen($materialSearch) >= 2)
                            <ul class="absolute z-50 mt-1 max-h-60 w-full overflow-y-auto rounded-md border border-slate-200 bg-white shadow-lg">
                                @forelse($materialSearchResults as $result)
                                    <li wire:key="manual-material-result-{{ $result['id'] }}">
                                        <button type="button" wire:click="addMaterial({{ $result['id'] }})" class="w-full px-4 py-3 text-left hover:bg-[#f2f7f6]">
                                            <span class="block font-medium text-slate-800">{{ $result['name'] }}</span>
                                            <span class="block text-sm text-slate-500">Артикул: {{ $result['article'] ?: '—' }} · Залишок: {{ $result['balance'] }}</span>
                                        </button>
                                    </li>
                                @empty
                                    <li class="px-4 py-3 text-sm text-slate-500">Матеріалів не знайдено</li>
                                @endforelse
                            </ul>
                        @endif
                    </div>
                    @if($materialSearchMessage)
                        <p class="text-sm text-amber-700">{{ $materialSearchMessage }}</p>
                    @endif
                    @error('issuanceItems')<p class="text-sm text-red-600">Додайте хоча б один матеріал.</p>@enderror
                </div>

                @if($issuanceItems)
                    <div class="space-y-3">
                        <h3 class="text-sm font-semibold text-[#245b53]">Позиції документа</h3>
                        @foreach($issuanceItems as $index => $item)
                            <div wire:key="manual-issuance-item-{{ $index }}" class="grid grid-cols-12 items-start gap-3 rounded-md border border-[#d4e5e0] p-3">
                                <div class="col-span-12 sm:col-span-7">
                                    <p class="font-medium text-slate-800">{{ $item['name'] }}</p>
                                    <p class="text-sm text-slate-500">Артикул: {{ $item['article'] ?: '—' }} · Залишок: {{ $item['balance'] }}</p>
                                </div>
                                <label class="col-span-8 sm:col-span-3">
                                    <span class="text-sm text-[#245b53]">Кількість</span>
                                    <input type="number" step="0.01" min="0.01" wire:model="issuanceItems.{{ $index }}.quantity" class="mt-1 block w-full rounded-md border-slate-300 focus:border-teal-600 focus:ring-teal-600">
                                    @error('issuanceItems.'.$index.'.quantity')<span class="mt-1 block text-sm text-red-600">{{ $message }}</span>@enderror
                                </label>
                                <button type="button" wire:click="removeMaterial({{ $index }})" aria-label="Видалити {{ $item['name'] }}" class="col-span-4 mt-6 rounded-md px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-50 sm:col-span-2">Видалити</button>
                            </div>
                        @endforeach
                    </div>
                @endif

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
