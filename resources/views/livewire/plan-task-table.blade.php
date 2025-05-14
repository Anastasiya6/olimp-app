<div>
    <div class="space-y-6">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="sm:flex sm:justify-between px-6 py-3">
                <a style="text-decoration: underline;" href="{{ route('plan-tasks.all',['order_name_id'=> $selectedOrder,'sender_department' => $sender_department_id,'receiver_department' => $receiver_department_id]) }}" target="_blank">
                    План у Pdf
                </a>
                <a style="text-decoration: underline;" href="{{ route('plan-task.specification.norm',['order_name_id'=> $selectedOrder,'sender_department' => $sender_department_id, 'receiver_department' => $receiver_department_id, 'type_report_in' => 'Pdf', 'with_purchased' => $with_purchased, 'with_material_purchased' => $with_material_purchased]) }}" target="_blank">
                    Специфіковані норми у Pdf
                </a>
                <a style="text-decoration: underline;" href="{{ route('plan-task.detail.specification.norm',['order_name_id'=> $selectedOrder,'sender_department' => $sender_department_id, 'receiver_department' => $receiver_department_id, 'type_report_in' => 'Pdf','with_purchased' => $with_purchased, 'with_material_purchased' => $with_material_purchased]) }}" target="_blank">
                    Подетально-специфіковані норми у Pdf
                </a>
                <a style="text-decoration: underline;" href="{{ route('plan-task.specification.norm',['order_name_id'=> $selectedOrder,'sender_department' => $sender_department_id, 'receiver_department' => $receiver_department_id, 'type_report_in' => 'Excel','with_purchased' => $with_purchased, 'with_material_purchased' => $with_material_purchased]) }}" target="_blank">
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
                    <option value="0">
                        Всі цеха
                    </option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}"
                            @if($department->id == $receiver_department_id) selected @endif>
                            {{ $department->number }}
                        </option>
                    @endforeach
                </select>
                <div class="flex justify-between items-center py-4 gap-4">

                    <div class="flex items-center gap-4">
                        <div class="py-4">
                            <input type="checkbox" wire:model="with_purchased" id="with_purchased" wire:change="updateSearch">
                            <label for="with_purchased" class="ml-1 font-semibold text-gray-800 text-base">З покупними</label>
                        </div>
                        <div class="py-4">
                            <input type="checkbox" wire:model="with_material_purchased" id="with_material_purchased" wire:change="updateSearch">
                            <label for="with_material_purchased" class="ml-1 font-semibold text-gray-800 text-base">Заміна матеріалів</label>
                        </div>
                    </div>
                </div>
        {{--                <button wire:click="viewConfirm"--}}
        {{--                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">--}}
        {{--                    Перенести дані з відом. застосув. у план--}}
        {{--                </button>--}}
{{--                    </div>--}}
                <div>
                    @if(session()->has('message'))
                        <div>{{ session('message') }}</div>
                    @endif

                </div>
            </div>

            <div class="overflow-hidden overflow-x-auto border-b border-gray-200 bg-white px-6">
                <div class="flex justify-between items-center py-4 gap-4">
                    <a href="{{ route($route.'.create',['order_name_id'=> $selectedOrder,'sender_department' => $sender_department_id,'receiver_department' => $receiver_department_id]) }}"
                       class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                        Створити
                    </a>
                </div>

                <div class="py-4">
                    <input type="text" wire:model.live="searchTerm" wire:keydown="updateSearch" placeholder="Пошук по номеру деталі"/>
                </div>
            </div>
            <div class="overflow-x-auto rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200 bg-white">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Мате-ріал
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Деталь
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Найменування деталі
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Застосов-ність
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Загальна кіл-ть
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Замовл.
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Цех відправник
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Цех отримувач
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                           Додано
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                           З покуп.
                        </th>
                        <th class="px-4 py-3">
                        </th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">

                    @foreach($items as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">

                                    <div class="py-4">
                                        <input type="checkbox" disabled  name="material" @if($item->material) checked @endif id="exactMatchCheckbox" value="1">
                                    </div>

                            </td>
                            <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                                {{ $item->designation->designation??'' }}
                            </td>
                            <td class="px-4 py-4  font-bold text-gray-900">
                                {{ $item->designation->name??'' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                                {{ $item->quantity??'' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                                {{ $item->quantity_total??'' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                                {{ $item->orderName->name??'' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                                {{ $item->senderDepartment->number??'' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                                {{ $item->receiverDepartment->number??'' }}
                            </td>
                            <td class="px-4 py-4 font-bold text-gray-900">
                                @if($item->is_report_application_statement==1)
                                        З відом застосув.
                                    @elseif($item->is_report_application_statement==2)
                                        Зі здаточ.
                                     @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                                <div class="py-4">
                                    <input type="checkbox" disabled  name="with_purchased" @if($item->with_purchased) checked @endif id="exactMatchCheckbox" value="1">
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-right">
                                <div class="flex items-center justify-end gap-2">
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
