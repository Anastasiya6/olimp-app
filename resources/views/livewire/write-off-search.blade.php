<div>
    <div class="min-w-full align-middle">

        <div class="gap-4 sm:flex py-6">
            <label for="exactMatchCheckbox">Замовл.</label>

            <select wire:model="selectedOrder" style="width: 100px" wire:change="updateSearch" id="exactMatchCheckbox" name="order_id" class="block w-full mt-1 rounded-md">
                @foreach($orders as $order)
                    <option value="{{ $order->id }}">
                        {{ $order->name }}
                    </option>
                @endforeach
            </select>
            <label class="inline-flex items-center" for="departmentCheckbox">Цех відпр.</label>

            <select wire:model.change="selectedDepartmentSender" wire:change="updateSearch" style="width: 100px" id="departmentCheckbox"
                    class="block rounded-md">
                @foreach($departments as $department)
                    <option value="{{ $department->id }}"
                            @if($department->id==$default_first_department) selected @endif 'selected'>
                        {{ $department->number }}
                    </option>
                @endforeach
            </select>
            <label class="inline-flex items-center" for="departmentCheckbox">Цех отрим.</label>

            <select wire:model.change="selectedDepartmentReceiver" wire:change="updateSearch" style="width: 100px" id="exactMatchCheckbox"
                    class="block rounded-md">
                @foreach($departments as $department)
                    <option value="{{ $department->id }}"
                            @if($department->id==$default_second_department) selected @endif 'selected'>
                        {{ $department->number }}
                    </option>
                @endforeach
            </select>
            <label for="start">Дата з:</label>

            <input type="date" wire:model.change="startDate" wire:change="updateSearch" id="start" name="trip-start"/>

            <label for="start">Дата по:</label>

            <input type="date" wire:model.change="endDate" id="start" name="trip-start"/>
            <button  wire:click="viewConfirm"
                     class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                Зформувати звіт
            </button>
        </div>
        <table class="min-w-full border divide-y divide-gray-200">
            <thead>
            <tr>
                <th class="bg-gray-50 px-6 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">На обр.</span>
                </th>
                <th class="bg-gray-50 px-6 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Номер докум.</span>
                </th>
                <th class="bg-gray-50 px-6 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Дата внес.</span>
                </th>
                <th class="bg-gray-50 px-6 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Дата докум.</span>
                </th>
                <th class="bg-gray-50 px-6 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Деталь</span>
                </th>

                <th class="bg-gray-50 px-6 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Замов-лення</span>
                </th>
                <th class="bg-gray-50 px-6 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Кіл-ть</span>
                </th>
                <th class="bg-gray-50 px-6 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Цех відпр.</span>
                </th>
                <th class="bg-gray-50 px-6 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Цех замовн.</span>
                </th>
                <th class="bg-gray-50 px-6 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Мате-ріал</span>
                </th>
                <th class="w-56 bg-gray-50 px-6 py-3 text-left">
                </th>
            </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200 divide-solid">
            @foreach($items as $item)
                    <td wire:key="{{ $item->id }}" class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                        <div class="py-4">
                            <input type="checkbox" value="{{$item->id}}" wire:model="selectedItems" wire:key="{{ $item->id }}"/>
                        </div>
                    </td>
                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                        <strong>{{ $item->document_number??'' }}</strong>
                    </td>
                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                        <strong>{{$item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d.m.Y') : '' }}</strong>
                    </td>
                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                        <strong>{{$item->document_date ? \Carbon\Carbon::parse($item->document_date)->format('d.m.Y') : '' }}</strong>
                    </td>
                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                        <strong>{{ $item->designation->designation??'' }}</strong>
                    </td>
                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                        <strong>{{ $item->order_name->name??'' }}</strong>
                    </td>
                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                        <strong>{{ $item->quantity??'' }}</strong>
                    </td>
                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                        <strong>{{ $item->senderDepartment->number??'' }}</strong>
                    </td>
                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                        <strong>{{ $item->receiverDepartment->number??'' }}</strong>
                    </td>
                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                        <strong>
                            <div class="py-4">
                                <input type="checkbox" disabled  name="material" @if($item->material) checked @endif id="exactMatchCheckbox" value="1">
                            </div>
                        </strong>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
       {{--<div class="py-4">
            {{ $items->appends(request()->input())->links() }}
        </div>--}}
    </div>
    <x-modal-window name="viewLog" title="">
        <x-slot:body>
            <div class="sm:flex sm:justify-center px-3 py-3 text-xl font-semibold">
                Списання
            </div>
            <div class="sm:flex sm:justify-center text-lg px-3 py-3">
                З &nbsp<b>{{\Carbon\Carbon::parse($startDate)->format('d.m.Y')}}</b>&nbsp по &nbsp<b>{{\Carbon\Carbon::parse($endDate)->format('d.m.Y')}}</b>
            </div>
            <div class="sm:flex sm:justify-center text-lg px-6 py-6">
                Замовлення &nbsp<b>№{{$selectedOrder}}</b>
            </div>
            <table class="min-w-full border divide-y divide-gray-200">
                <thead>
                <tr>
                    <th class="bg-gray-50 px-6 py-3 text-center">
                        <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Дата внесення</span>
                    </th>
                    <th class="bg-gray-50 px-6 py-3 text-center">
                        <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Деталь</span>
                    </th>
                    <th class="bg-gray-50 px-6 py-3 text-center">
                        <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Документ</span>
                    </th>
                    <th class="bg-gray-50 px-6 py-3 text-center">
                        <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Кількість</span>
                    </th>
                    {{--<th class="bg-gray-50 px-6 py-3 text-center">
                        <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Замовлення</span>
                    </th>--}}
                   {{-- <th class="bg-gray-50 px-6 py-3 text-center">
                        <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Матеріал</span>
                    </th>
                    <th class="bg-gray-50 px-6 py-3 text-center">
                        <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Норма</span>
                    </th>--}}
                </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200 divide-solid">
                @if($selectedDeliveryNotes)
                    @foreach($selectedDeliveryNotes as $item)
                        <tr class="bg-white">
                            <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                <strong>{{\Carbon\Carbon::parse($item->created_at)->format('d.m.Y')}}</strong>
                            </td>
                            <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                <strong>{!! $item->designation->designation !!}</strong>
                            </td>
                            <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                <strong>{!! $item->document_number !!}</strong>
                            </td>
                            <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                <strong>{!! $item->quantity !!}</strong>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            <div class="sm:flex sm:justify-center px-6 py-6">
                <a class="link-underline" href="{{ route('report.write.off', ['ids' => json_encode($this->selectedItems), 'order_name_id' => $this->selectedOrder, 'start_date' => $startDate, 'end_date' => $endDate, 'sender_department' => $this->selectedDepartmentSender,'receiver_department' => $this->selectedDepartmentReceiver]) }}" target="_blank">
                    <b>Звіт pdf</b>
                </a>
         </div>
        </x-slot:body>
    </x-modal-window>
</div>


