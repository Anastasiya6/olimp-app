<div class="min-w-full align-middle">

    <table class="min-w-full border divide-y divide-gray-200">
        <thead>
        <tr>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="font-medium leading-4 tracking-wider text-gray-500">Заказ №</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="font-medium leading-4 tracking-wider text-gray-500">Виріб</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="font-medium leading-4 tracking-wider text-gray-500 whitespace-no-wrap">Кіл-ть</span>
            </th>
            {{--<th class="bg-gray-50 px-6 py-3 text-left">
                <span class="font-medium leading-4 tracking-wider text-gray-500">Цех</span>
            </th>--}}
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="font-medium leading-4 tracking-wider text-gray-500">Відомість застос.</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="font-medium leading-4 tracking-wider text-gray-500">Специфік. норми витрат</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="font-medium leading-4 tracking-wider text-gray-500">Подетально-специфік. норми витрат</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="font-medium leading-4 tracking-wider text-gray-500">Відсутні норми матер-в</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="font-medium leading-4 tracking-wider text-gray-500">Комлектов. відомість</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="font-medium leading-4 tracking-wider text-gray-500">Комлектов. відомість на деталь</span>
            </th>
            <th class="w-56 bg-gray-50 px-6 py-3 text-left">
            </th>
        </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200 divide-solid">

        @foreach($items as $item)
            <tr class="bg-white">
                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                    <strong>{{ $item->order_number??'' }}</strong>
                </td>
                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                    <strong>{{ $item->designation->designation }}</strong>
                </td>
                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                    <strong>{{ $item->quantity??'' }}</strong>
                </td>
                {{--<td class="py-4 leading-5 text-gray-900 whitespace-no-wrap">
                    <select wire:model.change="selectedDepartment" name="designation_type_unit_id" class="block w-full mt-1 rounded-md">
                        @foreach($departments as $department)
                            <option value="{{ $department->number }}"
                                    @if($department->number==$default_department) selected @endif 'selected' }}>
                            {{ $department->number }}
                            </option>
                        @endforeach
                    </select>
                </td>--}}
                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                    <a href="{{ route('application.statement', [ 'filter' => 1, 'order_number' => $item->order_number]) }}" class="underline-link" target="_blank">
                        Pdf
                    </a>
                </td>
                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                    <a href="{{ route('specification.material', ['order_number' => $item->order_number ]) }}" class="underline-link" target="_blank">
                        Pdf
                    </a>
                </td>
                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                    <a href="{{ route('detail.specification.material', ['department' => $selectedDepartment, 'order_number' => $item->order_number]) }}" class="underline-link" target="_blank">
                        Pdf
                    </a>
                </td>
                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                    <a href="{{ route('not.norm.material', ['department' => $selectedDepartment, 'order_number' => $item->order_number]) }}" class="underline-link" target="_blank">
                        Pdf
                    </a>
                </td>

                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                    @if(Illuminate\Support\Facades\Storage::exists('public/entry_detail_order_' . $item->order_number . '.pdf'))
                        <a href="{{asset('storage/entry_detail_order_' . $item->order_number . '.pdf')}}" class="underline-link" target="_blank">
                            Заказ № {{$item->order_number}}
                        </a>
                    @endif
                    </br></br>
                    <a href="{{ route('entry.detail', [ 'order_number' => $item->order_number]) }}" class="underline-link" target="_blank">
                        Сформувати звіт
                    </a>
                </td>
                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                    <div>
                        @if(session()->has('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                    <div class="py-4">
                        <input type="text" wire:model="designation_number" placeholder="Вузол"/>
                    </div>

                    <button wire:click="generateReportEntryDetailSpecification" class="underline-link">
                        Сформувати звіт
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="py-4">
        {{ $items->appends(request()->input())->links() }}
    </div>
    <style>
        .vertical-text {
            writing-mode: vertical-lr; /* Пишет текст сверху вниз */
            transform: rotate(180deg); /* Переворачивает текст так, чтобы он шел снизу вверх */
            text-align: center;
            white-space: nowrap; /* Обеспечивает, что текст не переносится на новую строку */
        }
    </style>
</div>
