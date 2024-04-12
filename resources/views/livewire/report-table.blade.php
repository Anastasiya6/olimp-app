<div class="min-w-full align-middle">
    <table class="min-w-full border divide-y divide-gray-200">
        <thead>
        <tr>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Заказ №</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Виріб</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Кількість</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Цех</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Специфіковані норми витрат</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Подетально-специфіковані норми витрат</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Відутні норми матеріалів</span>
            </th>
            <th class="bg-gray-50 px-6 py-3 text-left">
                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Комлектовочна відомість</span>
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
                <td style="width:270px" class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                    <select wire:model.change="selectedDepartment" name="designation_type_unit_id" class="block w-full mt-1 rounded-md">
                        @foreach($departments as $department)
                            <option value="{{ $department->number }}"
                                    @if($department->number==$default_department) selected @endif 'selected' }}>
                            {{ $department->number }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                    <a href="{{ route('specification.material') }}" class="underline-link" target="_blank">
                        Pdf
                    </a>
                </td>
                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                    <a href="{{ route('detail.specification.material', ['department' => $selectedDepartment]) }}" class="underline-link" target="_blank">
                        Pdf
                    </a>
                </td>
                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                    <a href="{{ route('not.norm.material', ['department' => $selectedDepartment]) }}" class="underline-link" target="_blank">
                        Pdf
                    </a>
                </td>
                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                    <a href="{{ route('entry.detail') }}" class="underline-link" target="_blank">
                        Pdf
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="py-4">
        {{ $items->appends(request()->input())->links() }}
    </div>
</div>
