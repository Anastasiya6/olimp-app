<x-small-modal-window name="viewOpening" title="">
    <x-slot:body>
        <div class="sm:flex sm:justify-center px-6 py-6 text-xl font-semibold">
            Вигрузка залишків з 1С
        </div>
        <div class="px-6 py-4">
            <input
                type="file"
                wire:model="file"
                accept=".xlsx,.xls"
                class="block w-full text-sm text-gray-700"
            >

            @error('file')
            <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
            @enderror
        </div>
        <div class="sm:flex sm:justify-center px-6 py-6">
            <button wire:click="confirmExport"
                    @disabled(!$file)
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm">
                Вигрузити
            </button>
        </div>
    </x-slot:body>
</x-small-modal-window>

<x-small-modal-window name="confirmExport" title="Підтвердження">
    <x-slot:body>

        <div class="text-center text-lg font-semibold mb-6">
            Дійсно вигрузити залишки з 1С?
        </div>

        <div class="flex justify-center gap-4">

            <!-- OK -->
            <x-loading-indicator></x-loading-indicator>
            <button
                wire:click="unloadingStock"
                class="px-4 py-2 bg-gray-300 rounded-md">
                OK
            </button>

            <!-- Cancel -->
            <button
                x-on:click="$dispatch('close-modal', { name: 'confirmExport' })"
                class="px-4 py-2 bg-gray-300 rounded-md">
                Cancel
            </button>

        </div>

    </x-slot:body>
</x-small-modal-window>
