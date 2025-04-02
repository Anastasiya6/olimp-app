<div class="min-w-full align-middle">
    <div class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
        <div>
            @if(session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>

    <div class=" gap-4 sm:flex py-6">

        <input class="block rounded-md" type="text" wire:model.live="designation_number" placeholder="Вузол"/>

        <label class="inline-flex items-center" for="exactMatchCheckbox">Цех</label>

        <select wire:model.change="selectedDepartmentEntry" style="width: 100px" id="exactMatchCheckbox" name="department_id1"
                class="block rounded-md">
                <option value="0">
                    Всі цеха
                </option>
            @foreach($departments as $department)
                <option value="{{ $department->number }}">
                    {{ $department->number }}
                </option>
            @endforeach
        </select>

        <a target="_blank" href="{{ route('entry.detail.designation', ['designation_number' => trim($designation_number),'department' => $selectedDepartmentEntry]) }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
            Сформувати звіт
        </a>

    </div>

    <table class="min-w-full border divide-y divide-gray-200">
        <thead>
        <tr>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="font-medium leading-4 tracking-wider text-gray-500">Замовлення №</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="font-medium leading-4 tracking-wider text-gray-500">Виріб</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="font-medium leading-4 tracking-wider text-gray-500 whitespace-no-wrap">Кіл-ть</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="font-medium leading-4 tracking-wider text-gray-500">Цех</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="font-medium leading-4 tracking-wider text-gray-500">Відомість застос.</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="font-medium leading-4 tracking-wider text-gray-500">Відомість застос.(покупні)</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="font-medium leading-4 tracking-wider text-gray-500">Специфік. норми витрат</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="font-medium leading-4 tracking-wider text-gray-500">Подетально-специфік. норми витрат</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="font-medium leading-4 tracking-wider text-gray-500">Цехові списки</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="font-medium leading-4 tracking-wider text-gray-500">Відсутні норми матер-в</span>
            </th>
           {{-- <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="font-medium leading-4 tracking-wider text-gray-500">Відомість по здаточним</span>
            </th>--}}
        </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200 divide-solid">

        @foreach($items as $item)
            <tr class="bg-white">
                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                    <strong>{{ $item->orderName->name??'' }}</strong>
                </td>
                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                    <strong>{{ $item->count_quantity==1?$item->designation:''  }}</strong>
                </td>
                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                    <strong>{{ $item->count_quantity==1?$item->quantity:'' }}</strong>
                </td>
                <td class="py-4 leading-5 text-gray-900 whitespace-no-wrap">
                    <select wire:model.change="selectedDepartment.{{ $item->order_name_id }}" name="department_id" class="block w-full mt-1 rounded-md">
                        <option value="0">
                            Всі цеха
                        </option>
                        @foreach($departments as $department)
                            <option value="{{ $department->number }}">
{{--@if($department->number==$default_department) selected @endif 'selected' }}>--}}
                                {{ $department->number }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                    <a href="{{ route('application.statement', [ 'filter' => 1, 'order_name_id' => $item->order_name_id,'department' => $selectedDepartment[$item->order_name_id]??0 ]) }}" class="underline-link" target="_blank">
                        Відом.<br>
                        заст.
                    </a>
                </td>
                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                    <a href="{{ route('application.statement', [ 'filter' => 2, 'order_name_id' => $item->order_name_id,'department' => $selectedDepartment[$item->order_name_id]??0 ]) }}" class="underline-link" target="_blank">
                        Відом.<br>
                        заст.(покуп.)
                    </a>
                </td>
                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                    <a href="{{ route('specification.material', ['order_name_id' => $item->order_name_id, 'department' => $selectedDepartment[$item->order_name_id]??0 ]) }}" class="underline-link" target="_blank">
                        Спец.<br>
                        н.в.
                    </a>
                </td>
                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                    <a href="{{ route('detail.specification.material', ['department' => $selectedDepartment[$item->order_name_id]??0 , 'order_name_id' => $item->order_name_id]) }}" class="underline-link" target="_blank">
                        Подет.<br>
                        спец.
                    </a>
                </td>
                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                    <a href="{{ route('department.list', ['filter' => 3,'department' => $selectedDepartment[$item->order_name_id]??0 , 'order_name_id' => $item->order_name_id]) }}" class="underline-link" target="_blank">
                        Цехові<br>
                        списки
                    </a>
                </td>
                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                    <a href="{{ route('not.norm.material', ['department' => $selectedDepartment[$item->order_name_id]??0, 'order_name_id' => $item->order_name_id]) }}" class="underline-link" target="_blank">
                        Відсут.<br>
                        н.м.
                    </a>
                </td>
                {{--<td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                    <a href="{{ route('delivery.notes', ['department' => $selectedDepartment, 'order_number' => $item->order_number]) }}" class="underline-link" target="_blank">
                        Pdf
                    </a>
                </td>--}}
               {{-- <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                    @if(Illuminate\Support\Facades\Storage::exists('public/entry_detail_order_' . $item->order_number . '.pdf'))
                        <a href="{{asset('storage/entry_detail_order_' . $item->order_number . '.pdf')}}" class="underline-link" target="_blank">
                            Замовлення № {{$item->order_number}}
                        </a>
                    @endif
                    </br></br>
                    <a href="{{ route('entry.detail', [ 'order_number' => $item->order_number]) }}" class="underline-link" target="_blank">
                        Сформувати звіт
                    </a>
                </td>--}}
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="py-4">
        {{ $items->appends(request()->input())->links() }}
    </div>
</div>
