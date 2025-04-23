<div>

    <div class="min-w-full align-middle">
        <div class="sm:flex sm:justify-between px-3 py-3">
            <div class="gap-4 sm:flex py-3">
                <label class="inline-flex items-center" for="departmentCheckbox">Цех отрим.</label>

                <select wire:model.change="selectedDepartmentSender" wire:change="updateSearch" style="width: 100px" id="exactMatchCheckbox"
                        class="block rounded-md">
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}"
                            @if($department->id==$default_first_department) selected @endif 'selected'>
                            {{ $department->number }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="gap-4 sm:flex py-3">
                <button  wire:click="viewConfirm"
                         class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                    Зформувати звіт
                </button>
            </div>
        </div>
        <div class="sm:flex sm:justify-between px-3 py-3">
            <a href="{{ route($route.'.create',['sender_department' => $selectedDepartmentSender,'type' => $type]) }}"
               class="mb-4 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                Створити
            </a>
            <button type="button"
                    class="mb-4 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25"
                    wire:click="deleteAllTask({{ $selectedDepartmentSender }})"
                    onclick="confirm('Ви впевнені, що хочете видалити всі деталі для даного цеху?') || event.stopImmediatePropagation();">
                Видалити деталі
            </button>
        </div>
        <div class="min-w-full align-middle">
            <table class="min-w-full border divide-y divide-gray-200">
                <thead>
                <tr>
                    <th class="bg-gray-50 px-6 py-3 text-left">
                        <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">На обр.</span>
                    </th>
                    <th class="bg-gray-50 px-6 py-3 text-left">
                        <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Деталь</span>
                    </th>
                    <th class="bg-gray-50 px-6 py-3 text-left">
                        <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Кількість</span>
                    </th>
                    <th class="bg-gray-50 px-6 py-3 text-left">
                        <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Відділ</span>
                    </th>
                    <th class="bg-gray-50 px-6 py-3 text-left">
                        <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Матеріал</span>
                    </th>
                    <th class="w-56 bg-gray-50 px-6 py-3 text-left">
                    </th>
                </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200 divide-solid">

                @foreach($items as $item)
                    <tr class="bg-white">
                        <td wire:key="{{ $item->id }}" class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                            <div class="py-4">
                                <input type="checkbox" value="{{$item->id}}" wire:model="selectedItems" wire:key="{{ $item->id }}"/>
                            </div>
                        </td>
                        <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                            <strong>{{ $item->designation->designation??'' }}</strong>
                        </td>
                        <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                            <strong>{{ $item->quantity??'' }}</strong>
                        </td>
                        <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                            <strong>{{ $item->department->number??'' }}</strong>
                        </td>
                        <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                            <strong>
                                <div class="py-4">
                                    <input type="checkbox" disabled  name="material" @if($item->material==1) checked @endif id="exactMatchCheckbox" value="1">
                                </div>
                            </strong>
                        </td>
                        <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                            <a href="{{ route($route.'.edit', ['type' => $type,'task' => $item])  }}"
                               class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                                Edit
                            </a>
                            <x-danger-button
                                wire:key="{{ $item->id }}"
                                wire:click="deleteTask({{ $item->id }})"
                                wire:confirm="Ви впевнені, що хочете видалити запис?">
                                Delete
                            </x-danger-button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <x-modal-window name="viewLog" title="">
        <x-slot:body>
            <div class="flex sm:justify-center text-xl font-semibold">
                Завдання
            </div>
            <div class="flex items-center justify-end px-3 py-3">
                <input type="checkbox" wire:model="without_coefficient" id="without_coefficient" wire:change="updateSearch">
                <label for="with_purchased" class="ml-1 font-semibold text-gray-800 text-base">Без коеф.</label>
            </div>
            <div class="sm:flex sm:justify-between px-3 py-3">
                <!-- Форма для "Подет.-специфіковані" -->
                <form action="{{ route('report.task.detail') }}" method="POST" target="_blank">
                    @csrf
                    @foreach($selectedDetails as $item)
                        <input type="hidden" name="ids[]" value="{{ $item->id }}">
                    @endforeach
                    <input type="hidden" name="sender_department" value="{{ $selectedDepartmentSender }}">
                    <input type="hidden" name="without_coefficient" value="{{$without_coefficient}}">
                    <input type="hidden" name="type_report" value="0">
                    <button type="submit" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-2 py-2 text-xs font-semibold uppercase text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                        Подет.-специфіковані
                    </button>
                </form>

                <!-- Форма для "Разом по матеріалам" -->
                <form action="{{ route('report.task.material') }}" method="POST" target="_blank">
                    @csrf
                    @foreach($selectedDetails as $item)
                        <input type="hidden" name="ids[]" value="{{ $item->id }}">
                    @endforeach
                    <input type="hidden" name="sender_department" value="{{ $selectedDepartmentSender }}">
                    <input type="hidden" name="type_report" value="1">
                    <input type="hidden" name="without_coefficient" value="{{$without_coefficient}}">
                    <input type="hidden" name="type_report_in" value="pdf">
                    <button type="submit" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-2 py-2 text-xs font-semibold uppercase text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                        Разом по матеріалам
                    </button>
                </form>

                <!-- Форма для "Разом по матеріалам Excel" -->
                <form action="{{ route('report.task.material') }}" method="POST">
                    @csrf
                    @foreach($selectedDetails as $item)
                        <input type="hidden" name="ids[]" value="{{ $item->id }}">
                    @endforeach
                    <input type="hidden" name="sender_department" value="{{ $selectedDepartmentSender }}">
                    <input type="hidden" name="type_report" value="1">
                    <input type="hidden" name="without_coefficient" value="{{$without_coefficient}}">
                    <input type="hidden" name="type_report_in" value="Excel">
                    <button type="submit" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-2 py-2 text-xs font-semibold uppercase text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                        Разом по матер. Excel
                    </button>
                </form>

                <!-- Форма для "Нема матеріалів" -->
                <form action="{{ route('report.task.no.material') }}" method="POST" target="_blank">
                    @csrf
                    @foreach($selectedDetails as $item)
                        <input type="hidden" name="ids[]" value="{{ $item->id }}">
                    @endforeach
                    <input type="hidden" name="sender_department" value="{{ $selectedDepartmentSender }}">
                    <input type="hidden" name="type_report" value="2">
                    <button type="submit" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-2 py-2 text-xs font-semibold uppercase text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                        Нема матеріалів
                    </button>
                </form>
            </div>
            <div class="sm:flex sm:justify-between px-3 py-3">

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
                    </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200 divide-solid">
                    @if($selectedDetails)
                        @foreach($selectedDetails as $item)
                            <tr class="bg-white">
                                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                    <strong>{{\Carbon\Carbon::parse($item->created_at)->format('d.m.Y')}}</strong>
                                </td>
                                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                    <strong>{!! $item->designation->designation !!}</strong>
                                </td>
                                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                    <strong>{!! $item->quantity !!}</strong>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>

            <!-- Остальная часть модального окна -->

        </x-slot:body>
    </x-modal-window>
</div>
