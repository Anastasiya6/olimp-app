<div class="write-off-page" x-data="{}" x-on:open-modal.window="if ($event.detail?.name === 'viewLog') $dispatch('open-modal', 'write-off-reports')" x-on:close-modal.window="if ($event.detail?.name === 'viewLog') $dispatch('close-modal', 'write-off-reports')">
    <div class="min-w-full align-middle">

<x-catalog.panel class="mb-4"><div class="flex flex-wrap items-end gap-3"><div class="w-full sm:w-40"><label for="write-off-filter-0" class="mb-1 block text-sm font-medium text-slate-700">Замовлення</label><select id="write-off-filter-0" class="h-9 w-full rounded-md border-slate-300 py-1.5 text-sm focus:border-teal-600 focus:ring-teal-600" wire:model="selectedOrder" wire:change="updateSearch" name="order_id">
                @foreach($orders as $order)
                    <option value="{{ $order->id }}">
                        {{ $order->name }}
                    </option>
                @endforeach
            </select></div><div class="w-full sm:w-40"><label for="write-off-filter-1" class="mb-1 block text-sm font-medium text-slate-700">Цех відправник</label><select id="write-off-filter-1" class="h-9 w-full rounded-md border-slate-300 py-1.5 text-sm focus:border-teal-600 focus:ring-teal-600" wire:model.change="selectedDepartmentSender" wire:change="updateSearch"
                   >
                @foreach($departments as $department)
                    <option value="{{ $department->id }}"
                            @if($department->id==$default_first_department) selected @endif>
                        {{ $department->number }}
                    </option>
                @endforeach
            </select></div><div class="w-full sm:w-40"><label for="write-off-filter-2" class="mb-1 block text-sm font-medium text-slate-700">Цех отримувач</label><select id="write-off-filter-2" class="h-9 w-full rounded-md border-slate-300 py-1.5 text-sm focus:border-teal-600 focus:ring-teal-600" wire:model.change="selectedDepartmentReceiver" wire:change="updateSearch"
                   >
                @foreach($departments as $department)
                    <option value="{{ $department->id }}"
                            @if($department->id==$default_second_department) selected @endif>
                        {{ $department->number }}
                    </option>
                @endforeach
            </select></div><div class="w-full sm:w-40"><label for="write-off-filter-3" class="mb-1 block text-sm font-medium text-slate-700">Дата з</label><input id="write-off-filter-3" class="h-9 w-full rounded-md border-slate-300 py-1.5 text-sm focus:border-teal-600 focus:ring-teal-600" type="date" wire:model.change="startDate" wire:change="updateSearch" name="trip-start"/></div><div class="w-full sm:w-40"><label for="write-off-filter-4" class="mb-1 block text-sm font-medium text-slate-700">Дата по</label><input id="write-off-filter-4" class="h-9 w-full rounded-md border-slate-300 py-1.5 text-sm focus:border-teal-600 focus:ring-teal-600" type="date" wire:model.change="endDate" name="trip-start"/></div><button type="button" wire:click="viewConfirm" class="catalog-button catalog-reports-button sm:ml-auto">
<svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Zm0 0v6h6M8 17v-3m4 3v-6m4 6v-2" /></svg>
Сформувати звіт</button></div></x-catalog.panel>
        <div class="overflow-hidden rounded-lg border border-[#a8c8c5] bg-white shadow-sm"><div class="overflow-x-auto"><table class="catalog-table">
            <thead>
            <tr>
                <th scope="col">
                    На обр.
                </th>
                <th scope="col">
                    Номер докум.
                </th>
                <th scope="col">
                    Дата внес.
                </th>
                <th scope="col">
                    Дата докум.
                </th>
                <th scope="col">
                    Деталь
                </th>

                <th scope="col">
                    Замовлення
                </th>
                <th scope="col">
                    Кіл-ть
                </th>
                <th scope="col">
                    Цех відпр.
                </th>
                <th scope="col">
                    Цех замовн.
                </th>
                <th scope="col">
                    З покуп.
                </th>
                <th scope="col">
                    Заміна матеріал.
                </th>
                <th scope="col">
                    Матеріал
                </th>

            </tr>
            </thead>

            <tbody>
            @foreach($items as $item)
                <tr>
                    <td wire:key="{{ $item->id }}" class="text-center">
                        <div>
                            <input type="checkbox" value="{{$item->id}}" wire:model="selectedItems" wire:key="{{ $item->id }}"/>
                        </div>
                    </td>
                    <td class="whitespace-nowrap">
                        {{ $item->document_number??'' }}
                    </td>
                    <td class="whitespace-nowrap">
                        {{$item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d.m.Y') : '' }}
                    </td>
                    <td class="whitespace-nowrap">
                        {{$item->document_date ? \Carbon\Carbon::parse($item->document_date)->format('d.m.Y') : '' }}
                    </td>
                    <td class="whitespace-nowrap">
                        {{ $item->designation->designation??'' }}
                    </td>
                    <td class="whitespace-nowrap">
                        {{ $item->orderName->name??'' }}
                    </td>
                    <td class="whitespace-nowrap">
                        {{ $item->quantity??'' }}
                    </td>
                    <td class="whitespace-nowrap">
                        {{ $item->senderDepartment->number??'' }}
                    </td>
                    <td class="whitespace-nowrap">
                        {{ $item->receiverDepartment->number??'' }}
                    </td>
                    <td class="text-center">
                        
                            <div>
                                <input type="checkbox" disabled  name="with_purchased" @if($item->with_purchased) checked @endif id="exactMatchCheckbox" value="1">
                            </div>
                        
                    </td>
                    <td class="text-center">
                        
                            <div>
                                <input type="checkbox" disabled  name="with_material_purchased" @if($item->with_material_purchased) checked @endif id="exactMatchCheckbox" value="1">
                            </div>
                        
                    </td>
                    <td class="text-center">
                        
                            <div>
                                <p>{{$item->designationName}}</p>
                                <input type="checkbox" disabled  name="material" @if($item->material) checked @endif id="exactMatchCheckbox" value="1">
                            </div>
                        
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table></div></div>
       
    </div>
    <x-modal name="write-off-reports" maxWidth="7xl" focusable>
        <div role="dialog" aria-modal="true" aria-labelledby="write-off-reports-title">
            <div class="flex items-center justify-between border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4">
                <h3 id="write-off-reports-title" class="text-xl font-bold text-[#174a47]">Звіти списання</h3>
                <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити звіти" class="rounded-md p-2 text-[#245b53] hover:bg-white">✕</button>
            </div>
            <div class="space-y-4 p-5">

            <div class="flex flex-wrap justify-center text-base text-slate-700">
                З &nbsp<b>{{\Carbon\Carbon::parse($startDate)->format('d.m.Y')}}</b>&nbsp по &nbsp<b>{{\Carbon\Carbon::parse($endDate)->format('d.m.Y')}}</b>
            </div>
            <div class="flex flex-wrap justify-center text-base text-slate-700">
                Замовлення &nbsp<b>№{{$order_number}}</b>
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <a class="catalog-button catalog-button-secondary"
                   href="{{ route('report.write.off', ['ids' => json_encode($this->selectedItems), 'order_name_id' => $this->selectedOrder, 'start_date' => $startDate, 'end_date' => $endDate, 'sender_department' => $this->selectedDepartmentSender,'receiver_department' => $this->selectedDepartmentReceiver, 'type_report' => 0]) }}" target="_blank">
                    Подет.-специфіковані
                </a>
                <a class="catalog-button catalog-button-secondary"
                href="{{ route('report.write.off', ['ids' => json_encode($this->selectedItems), 'order_name_id' => $this->selectedOrder, 'start_date' => $startDate, 'end_date' => $endDate, 'sender_department' => $this->selectedDepartmentSender,'receiver_department' => $this->selectedDepartmentReceiver, 'type_report' => 2]) }}" target="_blank">
                    Здаточні
                </a>
                <a class="catalog-button catalog-button-secondary"
                   href="{{ route('report.write.off', ['ids' => json_encode($this->selectedItems), 'order_name_id' => $this->selectedOrder, 'start_date' => $startDate, 'end_date' => $endDate, 'sender_department' => $this->selectedDepartmentSender,'receiver_department' => $this->selectedDepartmentReceiver, 'type_report' => 1]) }}" target="_blank">
                    Разом по матеріалам
                </a>
                <a class="catalog-button catalog-button-secondary"
                   href="{{ route('report.write.off.no.material', ['order_name_id' => $this->selectedOrder, 'start_date' => $startDate, 'end_date' => $endDate, 'sender_department' => $this->selectedDepartmentSender,'receiver_department' => $this->selectedDepartmentReceiver]) }}" target="_blank">
                    Нема матеріалів
                </a>
            </div>
            <div class="overflow-hidden rounded-lg border border-[#a8c8c5] bg-white shadow-sm"><div class="overflow-x-auto"><table class="catalog-table">
                <thead>
                <tr>
                    <th scope="col">
                        Дата внесення
                    </th>
                    <th scope="col">
                        Деталь
                    </th>
                    <th scope="col">
                        Документ
                    </th>
                    <th scope="col">
                        Кількість
                    </th>
                    
                   
                </tr>
                </thead>

                <tbody>
                @if($selectedDeliveryNotes)
                    @foreach($selectedDeliveryNotes as $item)
                        <tr>
                            <td class="text-center">
                                {{\Carbon\Carbon::parse($item->created_at)->format('d.m.Y')}}
                            </td>
                            <td class="text-center">
                                {!! $item->designation->designation !!}
                            </td>
                            <td class="text-center">
                                {!! $item->document_number !!}
                            </td>
                            <td class="text-center">
                                {!! $item->quantity !!}
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table></div></div>
            <div class="flex justify-end border-t border-[#d4e5e0] pt-4"><x-catalog.button x-on:click="$dispatch('close')">Закрити</x-catalog.button></div>
            </div>
        </div>
    </x-modal>
</div>


