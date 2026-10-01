<x-catalog.page title="Створити завдання" width="max-w-3xl">
    <x-catalog.panel>
<form class="catalog-form" method="POST" action="{{ route($route.'.store',['type' => $type]) }}">
                        @csrf

                        <div class="delivery-note-designation-picker mb-5">@livewire('delivery-note-search-dropdown')</div>
                        <input type="hidden" name="type" value="{{ $type }}">
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Кількість</span>
                                <input type="text" name="quantity" class="catalog-input" placeholder=""
                                       value="{{ old('quantity') }}" />
                            </label>
                            @error('quantity')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <input type="hidden" name="department_id" value="{{ $sender_department_id }}">
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Цех відправник</span>
                                <input type="text" name="sender_department" readonly class="catalog-input" placeholder=""
                                       value="{{ $sender_department }}" />
                            </label>
                            @error('sender_department')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="flex justify-end gap-3 border-t border-[#d4e5e0] pt-5">
                        <x-catalog.button :href="route('tasks.index', ['type' => $type])">Скасувати</x-catalog.button>
                        <x-catalog.button variant="primary" type="submit">
                            Зберегти
                        </x-catalog.button></div>

                    </form>
    </x-catalog.panel>
</x-catalog.page>
