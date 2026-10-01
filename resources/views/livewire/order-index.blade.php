    <div class="orders-page" x-data="{ deleteId: null, deleteName: '', deleting: false, deleteError: '' }" x-on:order-edit-open.window="$dispatch('open-modal', 'edit-order')">
        <x-catalog.panel class="mb-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <input type="search" wire:model.live.debounce.300ms="orderSearch" aria-label="Пошук за замовленням" placeholder="Назва або номер замовлення" class="compact-search catalog-input" />
                <input type="search" wire:model.live.debounce.300ms="detailSearch" aria-label="Пошук за деталлю" placeholder="Позначення деталі" class="compact-search catalog-input" />
                <x-catalog.button type="button" x-on:click="$dispatch('open-modal', 'create-order')" aria-haspopup="dialog" variant="primary" class="catalog-add-button shrink-0 sm:ml-auto">
                    <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
                    Додати
                </x-catalog.button>
            </div>
        </x-catalog.panel>
        <div class="overflow-hidden rounded-lg border border-[#a8c8c5] bg-white shadow-sm">
            <div class="overflow-x-auto">
<table class="catalog-table">
                            <thead>
                            <tr>
                                <th scope="col">
                                    Є матеріали
                                </th>
                                <th scope="col">
                                    № замовл.
                                </th>
                                <th scope="col">
                                    Виріб
                                </th>
                                <th scope="col">
                                    Кіл-ть
                                </th>
                                <th scope="col">
                                    Розузлован
                                </th>
                                <th scope="col">
                                    Розузловати
                                </th>
                                <th scope="col" class="order-pdf-column">
                                    Відомість<br>застосування
                                </th>
                                <th scope="col" class="order-pdf-column" title="Відомість застосування (Покупні)">
                                    Відомість<br>покупні
                                </th>
                                <th scope="col">
                                    Дії
                                </th>
                            </tr>
                            </thead>

                            <tbody>

                            @forelse($items as $item)
                                <tr wire:key="order-row-{{ $item->id }}">
                                    <td>
                                        <strong>
                                            <div>
                                                <input type="checkbox" disabled  name="material" @if($item->is_material) checked @endif aria-label="Є матеріали" value="1">
                                            </div>
                                        </strong>
                                    </td>
                                    <td>
                                        {{ $item->orderName->name??'' }}
                                    </td>
                                    <td>
                                        {{ $item->designation->designation }}
                                    </td>
                                    <td>
                                        <strong>{{ $item->quantity??'' }}</strong>
                                    </td>
                                    <td>
                                        <livewire:update-data-report :order_name_id="$item->order_name_id" :report_date="$report_dates[$item->id] ?? null" :key="'order-report-'.$item->id"/>
                                    </td>
                                    <td>
                                        <livewire:disassembly :order_name_id="$item->order_name_id" :key="'order-disassembly-'.$item->id" />
                                    </td>
                                    <td class="order-pdf-column">
                                        <a class="catalog-button" href="{{ route('application.statement', [ 'filter' => 1, 'order_name_id' => $item->order_name_id, 'department' => 0]) }}" target="_blank">
                                            Pdf
                                        </a>
                                    </td>
                                    <td class="order-pdf-column">
                                        <a class="catalog-button" href="{{ route('application.statement', [ 'filter' => 2, 'order_name_id' => $item->order_name_id, 'department' => 0]) }}" target="_blank">
                                            Pdf
                                        </a>
                                    </td>
                                    <td class="order-actions">
                                        <div class="flex items-center gap-2">
                                        <x-catalog.button type="button" wire:click="editOrder({{ $item->id }})" wire:loading.attr="disabled" wire:target="editOrder" aria-haspopup="dialog">Редагувати</x-catalog.button>
                                        <x-catalog.button type="button" variant="danger"
                                            data-delete-name="{{ ($item->designation->designation ?? '').' — замовлення '.($item->orderName->name ?? '') }}"
                                            x-on:click="deleteId = {{ $item->id }}; deleteName = $el.dataset.deleteName; deleteError = ''; $dispatch('open-modal', 'delete-order')">
                                                Видалити
                                        </x-catalog.button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center">Записів не знайдено.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
            </div>
            <div class="border-t border-[#a8c8c5] bg-white px-4 py-4">
                <p class="mb-3 text-sm text-slate-600">Записів: {{ $items->total() }}</p>
                {{ $items->links('livewire.pagination.material-stocks') }}
            </div>
        </div>

        <x-modal name="create-order" maxWidth="2xl" :show="(bool) old('_order_create') && $errors->any()" focusable>
            <div role="dialog" aria-modal="true" aria-labelledby="create-order-title">
                <div class="flex items-center justify-between border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4">
                    <h3 id="create-order-title" class="text-xl font-bold text-[#174a47]">Додати розузловання</h3>
                    <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити" class="rounded-md p-2 text-[#245b53]">
                        <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="p-5 sm:p-6">
<form class="catalog-form" method="POST" action="{{ route($route.'.store') }}">
                        @csrf
                        <input type="hidden" name="_order_create" value="1" />
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Виберіть замовлення</span>
                                <select name="order_name_id" class="catalog-input">
                                    @foreach($order_names as $order_name)
                                        <option value="{{ $order_name->id }}" @selected((string) old('order_name_id') === (string) $order_name->id)>
                                            {{ $order_name->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                            @error('order_name_id')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Деталь</span>
                                <input type="text" name="designation"
                                       class="catalog-input"
                                       placeholder="" value="{{old('designation')}}" />
                            </label>
                            @error('designation')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Кількість</span>
                                <input type="text" name="quantity"
                                       class="catalog-input"
                                       placeholder="" value="{{old('quantity')}}" />
                            </label>
                            @error('quantity')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="flex flex-wrap justify-end gap-3 border-t border-[#d4e5e0] pt-5">
                            <x-catalog.button type="button" x-on:click="$dispatch('close')">Скасувати</x-catalog.button>
                            <x-catalog.button variant="primary" type="submit">Зберегти</x-catalog.button>
                        </div>
</form>
                </div>
            </div>
        </x-modal>
        <x-modal name="edit-order" maxWidth="2xl" :show="(bool) old('_order_edit') && $errors->any()" focusable>
            <div role="dialog" aria-modal="true" aria-labelledby="edit-order-title">
                <div class="flex items-center justify-between border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4">
                    <h3 id="edit-order-title" class="text-xl font-bold text-[#174a47]">Редагувати розузловання</h3>
                    <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити" class="rounded-md p-2 text-[#245b53]">
                        <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                @if($editingOrder)
                <div class="p-5 sm:p-6" wire:key="edit-order-form-{{ $editingOrder->id }}-{{ $editFormVersion }}">
<form class="catalog-form" method="POST" action="{{ route($route.'.update', $editingOrder->id) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_order_edit" value="{{ $editingOrder->id }}" />
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Виберіть замовлення</span>
                                <select name="order_name_id" class="catalog-input">
                                    @foreach($order_names as $order_name)
                                        <option value="{{ $order_name->id }}" @selected((string) (old('_order_edit') == $editingOrder->id ? old('order_name_id', $editingOrder->order_name_id) : $editingOrder->order_name_id) === (string) $order_name->id)>
                                            {{ $order_name->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                            @error('order_name_id')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Деталь</span>
                                <input type="text" name="designation"
                                       class="catalog-input"
                                       placeholder="" value="{{old('_order_edit') == $editingOrder->id ? old('designation', $editingOrder->designation->designation) : $editingOrder->designation->designation}}" />
                            </label>
                            @error('designation')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Кількість</span>
                                <input type="text" name="quantity"
                                       class="catalog-input"
                                       placeholder="" value="{{old('_order_edit') == $editingOrder->id ? old('quantity', $editingOrder->quantity) : $editingOrder->quantity}}" />
                            </label>
                            @error('quantity')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="flex flex-wrap justify-end gap-3 border-t border-[#d4e5e0] pt-5">
                            <x-catalog.button type="button" x-on:click="$dispatch('close')">Скасувати</x-catalog.button>
                            <x-catalog.button variant="primary" type="submit">Зберегти</x-catalog.button>
                        </div>
</form>
                </div>
                @endif
            </div>
        </x-modal>
        <div wire:key="delete-order-dialog">
            <x-catalog.delete-dialog name="delete-order" method="deleteOrder" title="Видалити розузловання?" />
        </div>
    </div>

