<div class="reports-page">
    @if(session()->has('error'))
        <div role="alert" class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif
    <x-catalog.panel class="mb-4">
    <div class="flex flex-wrap items-center gap-3">

        <input aria-label="Позначення вузла" class="compact-search" type="text" wire:model.live="designation_number" placeholder="Позначення вузла"/>

        <label class="inline-flex items-center" for="exactMatchCheckbox">Цех</label>

        <select wire:model.change="selectedDepartmentEntry"  id="exactMatchCheckbox" name="department_id1"
                class="h-9 w-40 rounded-md border-slate-300 py-1.5 text-sm focus:border-teal-600 focus:ring-teal-600">
                <option value="0">
                    Всі цеха
                </option>
            @foreach($departments as $department)
                <option value="{{ $department->number }}">
                    {{ $department->number }}
                </option>
            @endforeach
        </select>

        <a target="_blank" href="{{ route('entry.detail.designation', ['designation_number' => trim($designation_number),'department' => $selectedDepartmentEntry]) }}" class="catalog-button catalog-reports-button">
            Сформувати звіт
        </a>

    </div>

    </x-catalog.panel>
    <div class="overflow-hidden rounded-lg border border-[#a8c8c5] bg-white shadow-sm"><div class="overflow-x-auto"><table class="catalog-table">
        <thead>
        <tr>
            <th scope="col">
                Замовлення №
            </th>
            <th scope="col">
                Виріб
            </th>
            <th scope="col">
                Кіл-ть
            </th>
            <th scope="col">
                Цех
            </th>
            <th scope="col">
                Відомість застос.
            </th>
            <th scope="col">
                Відомість застос.(покупні)
            </th>
            <th scope="col">
                Специфік. норми витрат
            </th>
            <th scope="col">
                Подетально-специфік. норми витрат
            </th>
            <th scope="col">
                Цехові списки
            </th>
            <th scope="col">
                Відсутні норми матер-в
            </th>
           
        </tr>
        </thead>

        <tbody>

        @foreach($items as $item)
            <tr>
                <td class="align-middle">
                    {{ $item->orderName->name??'' }}
                </td>
                <td class="align-middle">
                    {{ $item->count_quantity==1?$item->designation:''  }}
                </td>
                <td class="align-middle">
                    {{ $item->count_quantity==1?$item->quantity:'' }}
                </td>
                <td class="align-middle">
                    <select wire:model.change="selectedDepartment.{{ $item->order_name_id }}" name="department_id" aria-label="Цех для замовлення {{ $item->orderName->name ?? '' }}" class="min-w-[7rem] rounded-md border-[#a8c8c5] py-2 text-sm focus:border-teal-600 focus:ring-teal-600">
                        <option value="0">
                            Всі цеха
                        </option>
                        @foreach($departments as $department)
                            <option value="{{ $department->number }}">

                                {{ $department->number }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td class="text-center">
                    <a href="{{ route('application.statement', [ 'filter' => 1, 'order_name_id' => $item->order_name_id,'department' => $selectedDepartment[$item->order_name_id]??0 ]) }}" class="catalog-button catalog-button-secondary" target="_blank">
                        Відом.<br>
                        заст.
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{ route('application.statement', [ 'filter' => 2, 'order_name_id' => $item->order_name_id,'department' => $selectedDepartment[$item->order_name_id]??0 ]) }}" class="catalog-button catalog-button-secondary" target="_blank">
                        Відом.<br>
                        заст.(покуп.)
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{ route('specification.material', ['order_name_id' => $item->order_name_id, 'department' => $selectedDepartment[$item->order_name_id]??0 ]) }}" class="catalog-button catalog-button-secondary" target="_blank">
                        Спец.<br>
                        н.в.
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{ route('detail.specification.material', ['department' => $selectedDepartment[$item->order_name_id]??0 , 'order_name_id' => $item->order_name_id]) }}" class="catalog-button catalog-button-secondary" target="_blank">
                        Подет.<br>
                        спец.
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{ route('department.list', ['filter' => 3,'department' => $selectedDepartment[$item->order_name_id]??0 , 'order_name_id' => $item->order_name_id]) }}" class="catalog-button catalog-button-secondary" target="_blank">
                        Цехові<br>
                        списки
                    </a>
                </td>
                <td class="text-center">
                    <a href="{{ route('not.norm.material', ['department' => $selectedDepartment[$item->order_name_id]??0, 'order_name_id' => $item->order_name_id]) }}" class="catalog-button catalog-button-secondary" target="_blank">
                        Відсут.<br>
                        н.м.
                    </a>
                </td>
                
               
            </tr>
        @endforeach
        </tbody>
    </table></div>
    <div class="border-t border-[#a8c8c5] px-4 py-4">
        {{ $items->appends(request()->input())->links('livewire.pagination.material-stocks') }}
    </div>
</div>
</div>
