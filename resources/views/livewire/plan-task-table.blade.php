<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="sm:flex sm:justify-between px-6 py-3">
                <a style="text-decoration: underline;" href="{{ route('plan-tasks.all',['order_name_id'=> $selectedOrder,'sender_department' => $sender_department_id,'receiver_department' => $receiver_department_id]) }}" target="_blank">
                    План у Pdf
                </a>
                <a style="text-decoration: underline;" href="{{ route('plan-task.specification.norm',['order_name_id'=> $selectedOrder,'department' => $sender_department_id, 'type_report_in' => 'Pdf']) }}" target="_blank">
                    Специфіковані норми у Pdf
                </a>
                <a style="text-decoration: underline;" href="{{ route('plan-task.detail.specification.norm',['order_name_id'=> $selectedOrder,'department' => $sender_department_id, 'type_report_in' => 'Pdf']) }}" target="_blank">
                    Подетально-специфіковані норми у Pdf
                </a>
                <a style="text-decoration: underline;" href="{{ route('plan-task.specification.norm',['order_name_id'=> $selectedOrder,'department' => $sender_department_id, 'type_report_in' => 'Excel']) }}" target="_blank">
                    Специфіковані норми в Excel
                </a>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between py-6 px-6">

                <label for="exactMatchCheckbox">Замовлення</label>
                <select wire:model="selectedOrder" style="width: 150px" wire:change="updateSearch" id="exactMatchCheckbox" name="order_id" class="block w-full mt-1 rounded-md">
                    @foreach($order_names as $order_name)
                        <option value="{{ $order_name->id }}">
                            {{ $order_name->name }}
                        </option>
                    @endforeach
                </select>

                <label for="exactMatchCheckbox">З цеха</label>
                <select wire:model.change="sender_department_id" wire:change="updateSearch"  style="width: 100px" name="department_id1" class="block w-full mt-1 rounded-md">
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}"
                        @if($department->id == $sender_department_id) selected @endif>
                            {{ $department->number }}
                        </option>
                    @endforeach
                </select>

                <label for="exactMatchCheckbox">До цеху</label>
                <select wire:model="receiver_department_id" wire:change="updateSearch" style="width: 100px" name="department_id2" class="block w-full mt-1 rounded-md">
                     @foreach($departments as $department)
                        <option value="{{ $department->id }}"
                            @if($department->id == $receiver_department_id) selected @endif>
                            {{ $department->number }}
                        </option>
                    @endforeach
                </select>

                <button wire:click="viewConfirm"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                    Перенести дані з відом. застосув. у план
                </button>
            </div>
            <div>
                @if(session()->has('message'))
                    <div>{{ session('message') }}</div>
                @endif

            </div>
            <div class="overflow-hidden overflow-x-auto border-b border-gray-200 bg-white p-6">

                <a href="{{ route($route.'.create',['order_name_id'=> $selectedOrder,'sender_department' => $sender_department_id,'receiver_department' => $receiver_department_id]) }}"
                    class="mb-4 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                    Створити
                </a>
                <div class="gap-4 sm:flex py-3">
                    <input type="text" wire:model.live="searchTerm" wire:keydown="updateSearch" placeholder="Пошук по номеру деталі"/>
                </div>
                <div class="min-w-full align-middle">
                    <table class="min-w-full border divide-y divide-gray-200">
                        <thead>
                        <tr>
                            <th class="bg-gray-50 px-3 py-3 text-center">
                                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Деталь</span>
                            </th>
                            <th class="bg-gray-50 px-3 py-3 text-center">
                                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Найменування деталі</span>
                            </th>
                            <th class="bg-gray-50 px-3 py-3 text-center">
                                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Кіл-ть</span>
                            </th>
                            <th class="bg-gray-50 px-3 py-3 text-center">
                                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Загальна кіл-ть</span>
                            </th>
                            <th class="bg-gray-50 px-3 py-3 text-center">
                                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Замовл.</span>
                            </th>
                            <th class="bg-gray-50 px-3 py-3 text-center">
                                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Цех відправник</span>
                            </th>
                            <th class="bg-gray-50 px-3 py-3 text-center">
                                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Цех отримувач</span>
                            </th>
                            <th class="bg-gray-50 px-3 py-3 text-center">
                                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">З відомості застос.</span>
                            </th>
                            <th class="bg-gray-50 px-6 py-3 text-left">
                                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">З покуп.</span>
                            </th>
                            <th class="w-56 bg-gray-50 px-6 py-3 text-left">
                            </th>
                        </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200 divide-solid">

                        @foreach($items as $item)
                            <tr class="bg-white">

                                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                                    <strong>{{ $item->designation->designation??'' }}</strong>
                                </td>
                                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                                    <strong>{{ $item->designation->name??'' }}</strong>
                                </td>
                                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                    <strong>{{ $item->quantity??'' }}</strong>
                                </td>
                                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                    <strong>{{ $item->quantity_total??'' }}</strong>
                                </td>
                                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                    <strong>{{ $item->orderName->name??'' }}</strong>
                                </td>
                                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                    <strong>{{ $item->senderDepartment->number??'' }}</strong>
                                </td>
                                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                    <strong>{{ $item->receiverDepartment->number??'' }}</strong>
                                </td>
                                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                    <strong>{{ $item->is_report_application_statement? 'Так' : 'Ні' }}</strong>
                                </td>
                                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                    <strong>
                                        <div class="py-4">
                                            <input type="checkbox" disabled  name="with_purchased" @if($item->with_purchased) checked @endif id="exactMatchCheckbox" value="1">
                                        </div>
                                    </strong>
                                </td>
                                <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                    <a href="{{ route($route.'.edit', $item) }}"
                                       class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                                        Edit
                                    </a>
                                    <x-danger-button
                                        wire:key="{{ $item->id }}"
                                        wire:click="deletePlanTask({{ $item->id }})"
                                        wire:confirm="Ви впевнені, що хочете видалити запис?">
                                        Delete
                                    </x-danger-button>


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
    </div>

    <x-modal-window name="viewLog" title="">
        <x-slot:body>
            <div class="sm:flex sm:justify-center px-6 py-6 text-xl font-semibold">
                План з відомості застосування
            </div>
            <div class="sm:flex sm:justify-center text-lg px-6 py-6">
                Замовлення &nbsp<b>№{{$order_number}}</b>
            </div>
            <div class="sm:flex sm:justify-center text-lg px-6 py-6">
                З цеху &nbsp<b>{{$sender_department}}</b>
            </div>
            <div class="sm:flex sm:justify-center text-lg px-6 py-6">
                До цеху &nbsp<b>{{$receiver_department}}</b>
            </div>
            <div class="sm:flex sm:justify-center px-6 py-6">
                <x-loading-indicator></x-loading-indicator>
                <button wire:click="makeFromDisassembly"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                    Зформувати
                </button>
            </div>
        </x-slot:body>
    </x-modal-window>

</div>
