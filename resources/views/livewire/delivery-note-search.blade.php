<div>

    <div class="gap-4 sm:flex py-3">

        <label class="inline-flex items-center" for="exactMatchCheckbox">Замовл.</label>

        <select wire:model.change="selectedOrder" style="width: 100px" id="exactMatchCheckbox" name="order_id" class="block w-full mt-1 rounded-md">
            @foreach($order_names as $order_name)
                <option value="{{ $order_name->id }}">
                    {{ $order_name->name }}
                </option>
            @endforeach
        </select>
        <label class="inline-flex items-center" for="departmentCheckbox">Цех відпр.</label>

        <select wire:model.change="selectedDepartmentSender" style="width: 100px" id="departmentCheckbox"
                class="block rounded-md">
            @foreach($departments as $department)
                <option value="{{ $department->id }}"
                        @if($department->id==$default_first_department) selected @endif 'selected'>
                    {{ $department->number }}
                </option>
            @endforeach
        </select>
        <label class="inline-flex items-center" for="departmentCheckbox">Цех отрим.</label>

        <select wire:model.change="selectedDepartmentReceiver" style="width: 100px" id="exactMatchCheckbox"
                class="block rounded-md">
            @foreach($departments as $department)
                <option value="{{ $department->id }}"
                        @if($department->id==$default_second_department) selected @endif 'selected'>
                    {{ $department->number }}
                </option>
            @endforeach
        </select>
        <a target="_blank" href="{{ route('delivery.notes', ['sender_department' => $selectedDepartmentSender, 'receiver_department' => $selectedDepartmentReceiver,'order_name_id' => $selectedOrder]) }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
            Сформувати звіт
        </a>

     {{--   <a href="{{ route('specification.delivery.notes', ['sender_department' => $selectedDepartmentSender, 'receiver_department' => $selectedDepartmentReceiver,'order_number' => $selectedOrder]) }}" class="underline-link" target="_blank">
            Специфіковані норми Pdf
        </a>--}}
    </div>
    <div class="gap-4 sm:flex py-3">
        <input type="text" wire:model.live="searchTerm" wire:keydown="updateSearch" placeholder="Пошук по номеру деталі"/>
        <a target="_blank" href="{{ route('delivery.notes.designation', ['designation' => $searchTerm == null ? '0' : $searchTerm]) }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
            Сформувати звіт
        </a>
    </div>
    <div>
        @if(session()->has('message'))
            <div>{{ session('message') }}</div>
        @endif
    </div>
    <div class="min-w-full align-middle">
        <table class="min-w-full border divide-y divide-gray-200">
            <thead>
            <tr>
                <th class="bg-gray-50 px-6 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Деталь</span>
                </th>
                <th class="bg-gray-50 px-6 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Документ</span>
                </th>
                <th class="bg-gray-50 px-6 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Дата документу</span>
                </th>
                <th class="bg-gray-50 px-6 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Дата внесення</span>
                </th>
                <th class="bg-gray-50 px-6 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Замовл.</span>
                </th>
                <th class="bg-gray-50 px-6 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Кіл-ть</span>
                </th>
                <th class="bg-gray-50 px-6 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Цех відпр.</span>
                </th>
                <th class="bg-gray-50 px-6 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Цех отрим.</span>
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
                        <strong>{{ $item->document_number??'' }}</strong>
                    </td>
                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                        <strong>{{ \Carbon\Carbon::parse($item->document_date)->format('d.m.Y')??'' }}</strong>
                    </td>
                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                        <strong>{{ \Carbon\Carbon::parse($item->created_at)->format('d.m.Y')??'' }}</strong>
                    </td>
                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                        <strong>{{ $item->orderName->name??'' }}</strong>
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
                            wire:click="deleteDeliveryNote({{ $item->id }})">
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


