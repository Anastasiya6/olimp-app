<div>
    @if($show)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg w-[400px]">
                <livewire:import-material-stock-search-dropdown/>

                <div class="mt-4">
                    <label class="block">
                        <span class="text-gray-700">Кількість</span>
                        <input
                            type="number"
                            step="0.01"
                            wire:model="takeQty"
                            class="block w-full mt-1 rounded-md"
                        >
                    </label>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <x-secondary-button wire:click="$set('show', false)">Відмінити</x-secondary-button>
                    <x-primary-button wire:click="save">Зберегти</x-primary-button>
                </div>


            </div>
        </div>
    @endif

</div>
