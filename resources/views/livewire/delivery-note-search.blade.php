<div class="delivery-notes-page" x-data="{ deleteId: null, deleteName: '', deleting: false, deleteError: '' }" x-on:delivery-note-edit-open.window="$dispatch('open-modal', 'edit-delivery-note')">

    <div class="space-y-4">

        <x-modal name="delivery-note-reports" maxWidth="2xl" focusable>
            <div role="dialog" aria-modal="true" aria-labelledby="delivery-note-reports-title">
                <div class="flex items-center justify-between gap-3 border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4">
                    <h3 id="delivery-note-reports-title" class="text-xl font-bold text-[#174a47]">Звіти здаточних</h3>
                    <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити звіти" class="rounded-md p-2 text-[#245b53] hover:bg-white">✕</button>
                </div>
        {{-- Фільтри --}}
        <div class="p-5 sm:p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="flex flex-col">
                    <label for="exactMatchCheckbox" class="catalog-label">Замовлення</label>
                    <select wire:model.change="selectedOrder" id="exactMatchCheckbox" name="order_id" class="catalog-input">
                        @foreach($order_names as $order_name)
                            <option value="{{ $order_name->id }}">{{ $order_name->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="departmentSender" class="catalog-label">Цех відправник</label>
                    <select wire:model.change="selectedDepartmentSender" id="departmentSender" class="catalog-input">
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" @selected($department->id == $default_first_department)>
                                {{ $department->number }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="departmentReceiver" class="catalog-label">Цех отримувач</label>
                    <select wire:model.change="selectedDepartmentReceiver" id="departmentReceiver" class="catalog-input">
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" @selected($department->id == $default_second_department)>
                                {{ $department->number }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col">
                    <label class="catalog-label">Дата документа</label>
                    <input type="date" wire:model.change="selectedDocumentDate" class="catalog-input" />
                </div>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-3 border-t border-[#d4e5e0] pt-5 sm:grid-cols-2">
                <a target="_blank" href="{{ route('delivery.notes', ['sender_department' => $selectedDepartmentSender, 'receiver_department' => $selectedDepartmentReceiver, 'order_name_id' => $selectedOrder, 'document_date' => $selectedDocumentDate ]) }}" class="catalog-button catalog-button-secondary">
                    Здаточні по даті докум.
                </a>

                <a target="_blank" href="{{ route('delivery.notes.plan', ['sender_department' => $selectedDepartmentSender, 'receiver_department' => $selectedDepartmentReceiver, 'order_name_id' => $selectedOrder, 'type_report_in' => 'pdf']) }}" class="catalog-button catalog-button-secondary">
                    Порівняння з планом (PDF)
                </a>

                <a target="_blank" href="{{ route('delivery.notes.plan', ['sender_department' => $selectedDepartmentSender, 'receiver_department' => $selectedDepartmentReceiver, 'order_name_id' => $selectedOrder, 'type_report_in' => 'Excel']) }}" class="catalog-button catalog-button-secondary">
                    Порівняння з планом (Excel)
                </a>

                <a target="_blank" href="{{ route('report.not.in.application.statement', ['sender_department' => $selectedDepartmentSender, 'order_name_id' => $selectedOrder]) }}" class="catalog-button catalog-button-secondary">
                    Нема у відомості застос.
                </a>
            </div>

        </div>
                <div class="flex justify-end border-t border-[#d4e5e0] px-5 py-4">
                    <x-catalog.button x-on:click="$dispatch('close')">Закрити</x-catalog.button>
                </div>
            </div>
        </x-modal>
        <x-catalog.panel>
        <div class="flex flex-wrap items-center gap-3">
            <input type="text" wire:model.live="searchTerm" wire:keydown="updateSearch" aria-label="Пошук по номеру деталі" placeholder="Пошук по номеру деталі" class="compact-search rounded-md border-gray-300"/>
            <a target="_blank" href="{{ route('delivery.notes.designation', ['designation' => $searchTerm == null ? '0' : $searchTerm]) }}" class="catalog-button catalog-button-secondary">
                Деталь у здаточних
            </a>
            <x-catalog.button x-on:click="$dispatch('open-modal', 'delivery-note-reports')" aria-haspopup="dialog" class="catalog-reports-button"><svg aria-hidden="true" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Zm0 0v6h6M8 17v-3m4 3v-6m4 6v-2" /></svg>Звіти</x-catalog.button>
            <x-catalog.button x-on:click="$dispatch('open-modal', 'create-delivery-note')" aria-haspopup="dialog" variant="primary" class="catalog-add-button sm:ml-auto">
                <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
                Створити
            </x-catalog.button>
        </div>
        </x-catalog.panel>
        @if(session('show_modal') && session()->has('message'))
            <x-modal name="delivery-note-plan-added" :show="true" maxWidth="lg" focusable>
                <div role="dialog" aria-modal="true" aria-labelledby="delivery-note-plan-added-title">
                    <div class="flex items-center justify-between gap-3 border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4">
                        <h3 id="delivery-note-plan-added-title" class="text-xl font-bold text-[#174a47]">Деталь додано в план</h3>
                        <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити повідомлення" class="rounded-md p-2 text-[#245b53] hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-600">
                            <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="p-5">
                        <p class="break-words text-base text-slate-700">{{ session('message') }}</p>
                        <div class="mt-5 flex justify-end border-t border-[#d4e5e0] pt-4">
                            <x-catalog.button variant="primary" x-on:click="$dispatch('close')">Зрозуміло</x-catalog.button>
                        </div>
                    </div>
                </div>
            </x-modal>
        @endif

        <div class="overflow-hidden rounded-lg border border-[#a8c8c5] bg-white shadow-sm">
            <div class="overflow-x-auto">
            <table class="catalog-table">
                <thead>
                <tr>
                    <th scope="col">
                        Номер
                    </th>
                    <th scope="col">
                        Деталь
                    </th>
                    <th scope="col">
                        Докум.
                    </th>
                    <th scope="col">
                        Дата докум.
                    </th>
                    <th scope="col">
                        Замовл.
                    </th>
                    <th scope="col">
                        Кіл-ть
                    </th>
                    <th scope="col">
                        Цех відпр.
                    </th>
                    <th scope="col">
                        Цех отрим.
                    </th>
                    <th scope="col">
                        З покуп.
                    </th>
                    <th scope="col">
                        Заміна матеріал.
                    </th>
                    <th scope="col">Дії</th>
                </tr>
                </thead>

                <tbody>
                @foreach($items as $item)
                    <tr>
                        <td class="whitespace-nowrap">
                            {{ $item->designation->designation ?? '' }}
                        </td>
                        <td class="whitespace-nowrap">
                            {{ $item->designation->name ?? '' }}
                        </td>
                        <td class="whitespace-nowrap">
                            {{ $item->document_number ?? '' }}
                        </td>
                        <td class="whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($item->document_date)->format('d.m.Y') ?? '' }}
                        </td>
                        <td class="whitespace-nowrap">
                            {{ $item->orderName->name ?? '' }}
                        </td>
                        <td class="whitespace-nowrap">
                            {{ $item->quantity ?? '' }}
                        </td>
                        <td class="whitespace-nowrap">
                            {{ $item->senderDepartment->number ?? '' }}
                        </td>
                        <td class="whitespace-nowrap">
                            {{ $item->receiverDepartment->number ?? '' }}
                        </td>
                        <td class="text-center">
                            <input type="checkbox" disabled @if($item->with_purchased) checked @endif class="h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                        </td>
                        <td class="text-center">
                            <input type="checkbox" disabled @if($item->with_material_purchased) checked @endif class="h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                        </td>
                        <td class="whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <x-catalog.button wire:click="editDeliveryNote({{ $item->id }})" wire:loading.attr="disabled" wire:target="editDeliveryNote" aria-haspopup="dialog">Редагувати</x-catalog.button>
                                <x-catalog.button variant="danger"
                                    wire:key="{{ $item->id }}"
                                    ::disabled="deleting" aria-haspopup="dialog"
                                    data-delete-name="{{ 'Здаточна №'.$item->document_number.' — '.($item->designation->designation ?? '') }}"
                                    x-on:click="deleteId = {{ $item->id }}; deleteName = $el.dataset.deleteName; deleteError = ''; $dispatch('open-modal', 'delete-delivery-note')">
                                    Видалити
                                </x-catalog.button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            </div>

            <div class="border-t border-[#a8c8c5] px-4 py-4">
                <p class="text-sm text-slate-600">Записів: {{ $items->total() }}</p>
                @if($items->hasPages())
                    <div class="mt-3">{{ $items->appends(request()->input())->links('livewire.pagination.material-stocks') }}</div>
                @endif
            </div>
        </div>
    </div>



    @foreach(['create' => 'Створити здаточну', 'edit' => 'Редагувати здаточну'] as $mode => $modalTitle)
        <div wire:key="{{ $mode }}-delivery-note-modal">
            <x-modal :name="$mode.'-delivery-note'" maxWidth="2xl" :show="(bool) old('_delivery_note_'.$mode) && $errors->any()" focusable>
                <div role="dialog" aria-modal="true" aria-labelledby="{{ $mode }}-delivery-note-title">
                    <div class="flex items-center justify-between gap-3 border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4">
                        <h3 id="{{ $mode }}-delivery-note-title" class="text-xl font-bold text-[#174a47]">{{ $modalTitle }}</h3>
                        <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити форму" class="rounded-md p-2 text-[#245b53] hover:bg-white">✕</button>
                    </div>
                    @if($mode === 'create' || $editingDeliveryNote)
                        <div class="p-5 sm:p-6" wire:key="{{ $mode }}-delivery-note-form-{{ $mode === 'edit' ? $editingDeliveryNote->id.'-'.$editFormVersion : 'new' }}">
                            @include('admin.include.delivery-notes.modal-form', ['note' => $mode === 'edit' ? $editingDeliveryNote : null])
                        </div>
                    @endif
                </div>
            </x-modal>
        </div>
    @endforeach
    <div wire:key="delete-delivery-note-dialog">
        <x-catalog.delete-dialog name="delete-delivery-note" method="deleteDeliveryNote" />
    </div>
</div>

