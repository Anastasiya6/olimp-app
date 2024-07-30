<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between py-6 px-6">

                <label for="exactMatchCheckbox">Вибрати замовлення</label>
                <select wire:model="selectedOrder" style="width: 150px" wire:change="updateSearch" id="exactMatchCheckbox" name="order_id" class="block w-full mt-1 rounded-md">
                    @foreach($order_names as $order_name)
                        <option value="{{ $order_name->order_number }}">
                            {{ $order_name->name }}
                        </option>
                    @endforeach
                </select>

                <label for="exactMatchCheckbox">З цеха</label>
                <select wire:model.change="selectedDepartment1" wire:change="updateSearch"  style="width: 100px" name="department_id1" class="block w-full mt-1 rounded-md">
                    @foreach($departments as $department)
                        <option value="{{ $department->number }}">
                            {{ $department->number }}
                        </option>
                    @endforeach
                </select>

                <label for="exactMatchCheckbox">До цеху</label>
                <select wire:model="selectedDepartment2" wire:change="updateSearch" style="width: 100px" name="department_id2" class="block w-full mt-1 rounded-md">
                     @foreach($departments as $department)
                        <option value="{{ $department->number }}">
                            {{ $department->number }}
                        </option>
                    @endforeach
                </select>

                <button wire:click="viewConfirm"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                    Перенести дані з відом. застосув. у план
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between py-3 px-6">
                <a style="text-decoration: underline;" href="{{ route('pi0.all') }}" target="_blank">
                    План у Pdf
                </a>
            </div>
            <div>
                @if(session()->has('message'))
                    <div>{{ session('message') }}</div>
                @endif

            </div>
            <div class="overflow-hidden overflow-x-auto border-b border-gray-200 bg-white p-6">

                <a href="{{ route($route.'.create',['order_name_id'=> $selectedOrder]) }}"
                    class="mb-4 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                    Створити
                </a>

                <div class="min-w-full align-middle">
                    <table class="min-w-full border divide-y divide-gray-200">
                        <thead>
                        <tr>
                            <th class="bg-gray-50 px-3 py-3 text-center">
                                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Деталь</span>
                            </th>
                            {{--<th class="bg-gray-50 px-3 py-3 text-center">
                                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Шифр приналежності</span>
                            </th>--}}
                            <th class="bg-gray-50 px-3 py-3 text-center">
                                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Кіл-ть</span>
                            </th>
                            <th class="bg-gray-50 px-3 py-3 text-center">
                                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Замовлення №</span>
                            </th>
                            <th class="bg-gray-50 px-3 py-3 text-center">
                                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Маршрут</span>
                            </th>
                            <th class="w-56 bg-gray-50 px-6 py-3 text-left">
                            </th>
                        </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200 divide-solid">

                        @foreach($items as $item)
                            <tr class="bg-white">

                                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                                    <strong>{{ $item->designationEntry->designation??'' }}</strong>
                                </td>
                               {{--td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                                    <strong>{{ $item->category_code??'' }}</strong>
                                </td>--}}
                                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                    <strong>{{ $item->quantity??'' }}</strong>
                                </td>
                                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                    <strong>{{ $item->order_number??'' }}</strong>
                                </td>
                                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                    <strong>{{ $item->tm??'' }}</strong>
                                </td>
                                <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                    <a href="{{ route($route.'.edit', ['planTask' => $item, 'order_number' => $selectedOrder]) }}"
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
                    Замовлення &nbsp<b>№{{$selectedOrder}}</b>
                </div>
                <div class="sm:flex sm:justify-center text-lg px-6 py-6">
                    З цеху &nbsp<b>{{$selectedDepartment1}}</b>
                </div>
                <div class="sm:flex sm:justify-center text-lg px-6 py-6">
                    До цеху &nbsp<b>{{$selectedDepartment2}}</b>
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
