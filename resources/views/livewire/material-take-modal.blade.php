<div>
    @if($show)
        <div x-on:click="show = false" class="fixed inset-0 bg-gray-300 opacity-40"></div>
        <div class="fixed inset-0 backdrop-blur-sm bg-black/30 flex items-center justify-center z-50">

            <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6">

                {{-- HEADER --}}
                <div class="flex justify-between items-center border-b pb-3 mb-4">
                    <h2 class="text-lg font-semibold">
                        Видача матеріалу
                    </h2>

                    <button wire:click="$set('show', false)" class="text-gray-400 hover:text-gray-600">
                        ✕
                    </button>
                </div>

                {{-- INFO BLOCK --}}
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="text-sm text-gray-500">Деталь</div>
                    <div class="font-medium text-gray-800">
                        {{ $detail_name }}
                    </div>

                    <div class="mt-3 text-sm text-gray-500">Матеріал</div>
                    <div class="font-medium text-gray-800">
                        {{ $material_name }}
                    </div>
                </div>

                {{-- SEARCH --}}
                <div class="mb-4">
                    <livewire:import-material-stock-search-dropdown :material_id="$selectedMaterialId" :material_name="$selectedMaterial"/>
                </div>

                {{-- INPUT --}}
                <div class="mb-6">
                    <label class="block">
                        <span class="text-gray-700 text-sm">Кількість</span>
                        <input
                            type="number"
                            step="0.01"
                            wire:model="takeQty"
                            class="block w-full mt-1 rounded-lg border-gray-300 focus:ring focus:ring-blue-200"
                        >
                    </label>
                </div>

                {{-- INPUT --}}
                <div class="mb-6">
                    <label class="block">
                        <span class="text-gray-700 text-sm">Фактична кількість</span>
                        <input
                            type="number"
                            step="0.01"
                            wire:model="takeFactQty"
                            class="block w-full mt-1 rounded-lg border-gray-300 focus:ring focus:ring-blue-200"
                        >
                    </label>
                </div>

                {{-- ACTIONS --}}
                <div class="flex justify-end gap-3">
                    <x-secondary-button wire:click="$set('show', false)">
                        Відмінити
                    </x-secondary-button>

                    <x-primary-button wire:click="save">
                        Зберегти
                    </x-primary-button>
                </div>

            </div>
        </div>
    @endif
</div>
