<div>

    <div class="space-y-6">

        {{-- Фільтри --}}
        <div class="flex flex-wrap gap-4 justify-between items-center bg-gray-50 p-4 rounded-lg shadow-sm">
            <div class="flex flex-wrap gap-4 items-center bg-gray-50 p-4 rounded-lg">
                <div class="flex flex-col">
                    <label for="exactMatchCheckbox" class="text-sm font-medium text-gray-700">Замовлення</label>
                    <select wire:model.change="selectedOrder" id="exactMatchCheckbox" name="order_id" class="w-28 rounded-md border-gray-300">
                        @foreach($order_names as $order_name)
                            <option value="{{ $order_name->id }}">{{ $order_name->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="departmentSender" class="text-sm font-medium text-gray-700">Цех відпр.</label>
                    <select wire:model.change="selectedDepartmentSender" id="departmentSender" class="w-28 rounded-md border-gray-300">
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" @selected($department->id == $default_first_department)>
                                {{ $department->number }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="departmentReceiver" class="text-sm font-medium text-gray-700">Цех отрим.</label>
                    <select wire:model.change="selectedDepartmentReceiver" id="departmentReceiver" class="w-40 rounded-md border-gray-300">
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" @selected($department->id == $default_second_department)>
                                {{ $department->number }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700">Дата документа</label>
                    <input type="date" wire:model.change="selectedDocumentDate" class="w-40 rounded-md border-gray-300" />
                </div>
            </div>

            <div class="flex flex-wrap gap-2 pt-4">
                <a target="_blank" href="{{ route('delivery.notes', ['sender_department' => $selectedDepartmentSender, 'receiver_department' => $selectedDepartmentReceiver, 'order_name_id' => $selectedOrder, 'document_date' => $selectedDocumentDate ]) }}" class="btn-primary">
                    Здаточні по даті докум.
                </a>

                <a target="_blank" href="{{ route('delivery.notes.plan', ['sender_department' => $selectedDepartmentSender, 'receiver_department' => $selectedDepartmentReceiver, 'order_name_id' => $selectedOrder, 'type_report_in' => 'pdf']) }}" class="btn-primary">
                    Порівняння з планом (PDF)
                </a>

                <a target="_blank" href="{{ route('delivery.notes.plan', ['sender_department' => $selectedDepartmentSender, 'receiver_department' => $selectedDepartmentReceiver, 'order_name_id' => $selectedOrder, 'type_report_in' => 'Excel']) }}" class="btn-primary">
                    Порівняння з планом (Excel)
                </a>

                <a target="_blank" href="{{ route('report.not.in.application.statement', ['sender_department' => $selectedDepartmentSender, 'order_name_id' => $selectedOrder]) }}" class="btn-primary">
                    Нема у відомості застос.
                </a>
            </div>

        </div>
        <div class="flex gap-2">
            <input type="text" wire:model.live="searchTerm" wire:keydown="updateSearch" placeholder="Пошук по номеру деталі" class="rounded-md border-gray-300"/>
            <a target="_blank" href="{{ route('delivery.notes.designation', ['designation' => $searchTerm == null ? '0' : $searchTerm]) }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                Деталь у здаточних
            </a>
        </div>
        <div>
            @if(session()->has('message'))
                <div>{{ session('message') }}</div>
            @endif
        </div>

        <div class="overflow-x-auto rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200 bg-white">
                <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Номер
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Деталь
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Докум.
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Дата докум.
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Замовл.
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Кіл-ть
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Цех відпр.
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Цех отрим.
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        З покуп.
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Заміна матеріал.
                    </th>
                    <th class="px-4 py-3"></th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                @foreach($items as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                            {{ $item->designation->designation ?? '' }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                            {{ $item->designation->name ?? '' }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                            {{ $item->document_number ?? '' }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                            {{ \Carbon\Carbon::parse($item->document_date)->format('d.m.Y') ?? '' }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                            {{ $item->orderName->name ?? '' }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                            {{ $item->quantity ?? '' }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                            {{ $item->senderDepartment->number ?? '' }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                            {{ $item->receiverDepartment->number ?? '' }}
                        </td>
                        <td class="px-4 py-4 text-center">
                            <input type="checkbox" disabled @if($item->with_purchased) checked @endif class="h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                        </td>
                        <td class="px-4 py-4 text-center">
                            <input type="checkbox" disabled @if($item->with_material_purchased) checked @endif class="h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route($route.'.edit', $item) }}"
                                   class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                                    Edit
                                </a>
                                <x-danger-button
                                    wire:key="{{ $item->id }}"
                                    wire:click="deleteDeliveryNote({{ $item->id }})">
                                    Delete
                                </x-danger-button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="py-4">
                {{ $items->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>

