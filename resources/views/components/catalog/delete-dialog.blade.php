@props(['name', 'method', 'title' => 'Видалити запис?', 'description' => 'Запис буде видалено. Цю дію неможливо скасувати.'])
<x-modal :name="$name" maxWidth="lg" focusable>
    <div class="p-6" role="dialog" aria-modal="true" aria-labelledby="{{ $name }}-title">
        <div class="flex items-start justify-between gap-4 border-b border-[#d4e5e0] pb-4">
            <h3 id="{{ $name }}-title" class="text-xl font-bold text-slate-800">{{ $title }}</h3>
            <button type="button" x-on:click="$dispatch('close')" :disabled="deleting" aria-label="Закрити підтвердження"
                class="rounded-md p-2 text-slate-500 hover:bg-[#edf6f3] focus:outline-none focus:ring-2 focus:ring-teal-600">
                <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <p x-text="deleteName" class="mt-4 break-words rounded-lg border border-[#bfd8d1] bg-[#edf6f3] p-4 text-lg font-semibold text-[#174a47]"></p>
        <p class="mt-3 text-base text-slate-600">{{ $description }}</p>
        <p x-show="deleteError" x-text="deleteError" role="alert" class="mt-4 rounded-md border border-red-200 bg-red-50 p-3 text-base text-red-800"></p>
        <div class="mt-5 flex justify-end gap-3 border-t border-[#d4e5e0] pt-4">
            <x-catalog.button x-on:click="$dispatch('close')" ::disabled="deleting">Скасувати</x-catalog.button>
            <x-catalog.button variant="danger" ::disabled="deleting || !deleteId || !!deleteError"
                x-on:click="
                    if (deleting || !deleteId) return;
                    deleting = true;
                    deleteError = '';
                    try {
                        const result = await $wire.{{ $method }}(deleteId);
                        if (result === false) {
                            deleteError = $wire.material_message || 'Запис використовується. Видалення неможливе.';
                        } else {
                            $dispatch('close-modal', '{{ $name }}');
                        }
                    } catch (error) {
                        deleteError = 'Не вдалося видалити запис. Оновіть сторінку та перевірте дані.';
                    } finally {
                        deleting = false;
                    }
                ">
                <span x-text="deleting ? 'Видалення…' : 'Видалити'"></span>
            </x-catalog.button>
        </div>
    </div>
</x-modal>
