<div class="material-take-dialog">
    @if($show)
        <div x-on:click="show = false" class="fixed inset-0 bg-gray-900/40 z-[60]"></div>
        <div class="fixed inset-0 z-[60] overflow-y-auto px-3 py-6 sm:px-6">

            <div role="dialog" aria-modal="true" aria-labelledby="material-take-title" class="relative mx-auto w-full max-w-2xl rounded-lg border border-[#bfd8d1] bg-white shadow-xl">

                {{-- HEADER --}}
                <div class="flex items-center justify-between gap-3 rounded-t-lg border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4 sm:px-6">
                    <h2 id="material-take-title" class="text-xl font-bold text-[#174a47]">
                        Видача матеріалу
                    </h2>

                    <button type="button" wire:click="$set('show', false)" aria-label="Закрити форму видачі матеріалу" class="rounded-md p-2 text-[#245b53] hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-600">
                        ✕
                    </button>
                </div>

                <div class="p-5 sm:p-6">
                {{-- INFO BLOCK --}}
                <div class="mb-4 rounded-md border border-[#d4e5e0] bg-[#f2f7f6] p-4">
                    <div class="text-sm text-slate-500">Деталь</div>
                    <div class="break-words text-base font-semibold text-[#174a47]">
                        {{ $detail_name }}
                    </div>

                    <div class="mt-3 text-sm text-slate-500">Матеріал</div>
                    <div class="break-words text-base font-semibold text-[#174a47]">
                        {{ $material_name }}
                    </div>
                </div>

                {{-- SEARCH --}}
                <div class="material-take-picker mb-4">
                    <livewire:import-material-stock-search-dropdown :material_id="$selectedMaterialId" :material_name="$selectedMaterial"/>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                {{-- INPUT --}}
                <div class="mb-4">
                    <label class="block">
                        <span class="text-sm font-medium text-[#245b53]">Кількість</span>
                        <input
                            type="number"
                            step="0.01"
                            wire:model="takeQty"
                            class="mt-1 block w-full rounded-md border-slate-300 text-base focus:border-teal-600 focus:ring-teal-600"
                        >
                    </label>
                </div>

                {{-- INPUT --}}
                <div class="mb-4">
                    <label class="block">
                        <span class="text-sm font-medium text-[#245b53]">Фактична кількість</span>
                        <input
                            type="number"
                            step="0.01"
                            wire:model="takeFactQty"
                            class="mt-1 block w-full rounded-md border-slate-300 text-base focus:border-teal-600 focus:ring-teal-600"
                        >
                    </label>
                </div>

                </div>
                {{-- ACTIONS --}}
                <div class="flex flex-wrap justify-end gap-3 border-t border-[#d4e5e0] pt-5">
                    <x-catalog.button variant="secondary" wire:click="$set('show', false)">
                        Відмінити
                    </x-catalog.button>

                    <x-catalog.button variant="primary" wire:click="save">
                        Зберегти
                    </x-catalog.button>
                </div>

                </div>
            </div>
        </div>
    @endif
</div>
