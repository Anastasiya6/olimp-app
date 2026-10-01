<div class="plan-page" x-data="{ deleteId: null, deleteName: '', deleting: false, deleteError: '' }" x-on:plan-form-open.window="$dispatch('open-modal', 'plan-form')">
    <div class="space-y-4">
        <div>
            <x-catalog.panel class="mb-4">
                <div class="flex flex-wrap items-end gap-3">
<div class="w-full sm:w-44"><label for="plan-filter-0" class="mb-1 block text-sm font-medium text-slate-700">Замовлення</label><select id="plan-filter-0" wire:model="selectedOrder" wire:change="updateSearch" name="order_id" class="h-9 w-full rounded-md border-slate-300 py-1.5 text-sm focus:border-teal-600 focus:ring-teal-600">
                    @foreach($order_names as $order_name)
                        <option value="{{ $order_name->id }}">
                            {{ $order_name->name }}
                        </option>
                    @endforeach
                </select></div><div class="w-full sm:w-44"><label for="plan-filter-1" class="mb-1 block text-sm font-medium text-slate-700">Цех відправник</label><select id="plan-filter-1" wire:model.change="sender_department_id" wire:change="updateSearch"  name="department_id1" class="h-9 w-full rounded-md border-slate-300 py-1.5 text-sm focus:border-teal-600 focus:ring-teal-600">
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}"
                        @if($department->id == $sender_department_id) selected @endif>
                            {{ $department->number }}
                        </option>
                    @endforeach
                </select></div><div class="w-full sm:w-44"><label for="plan-filter-2" class="mb-1 block text-sm font-medium text-slate-700">Цех отримувач</label><select id="plan-filter-2" wire:model="receiver_department_id" wire:change="updateSearch" name="department_id2" class="h-9 w-full rounded-md border-slate-300 py-1.5 text-sm focus:border-teal-600 focus:ring-teal-600">
                    <option value="0">
                        Всі цеха
                    </option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}"
                            @if($department->id == $receiver_department_id) selected @endif>
                            {{ $department->number }}
                        </option>
                    @endforeach
                </select></div><div class="flex flex-wrap items-center gap-4 pb-1"><div class="flex items-center gap-2"><input type="checkbox" wire:model="with_purchased" id="with_purchased" wire:change="updateSearch">
                            <label for="with_purchased" class="ml-1 font-semibold text-gray-800 text-base">З покупними</label></div><div class="flex items-center gap-2"><input type="checkbox" wire:model="with_material_purchased" id="with_material_purchased" wire:change="updateSearch">
                            <label for="with_material_purchased" class="ml-1 font-semibold text-gray-800 text-base">Заміна матеріалів</label></div></div>
<div class="ml-auto flex items-center gap-3">
<x-catalog.button x-on:click="$dispatch('open-modal', 'plan-reports')" aria-haspopup="dialog" class="catalog-reports-button shrink-0">
                        <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Zm0 0v6h6M8 17v-3m4 3v-6m4 6v-2" /></svg>
                        Звіти
                    </x-catalog.button>
<button wire:click="viewConfirmFromOrder" class="catalog-button catalog-button-secondary">Перенести план</button>
</div>
                </div>
                <div class="mt-4 flex flex-wrap items-center gap-3">
<input class="compact-search" type="text" wire:model.live="searchTerm" wire:keydown="updateSearch" placeholder="Пошук по номеру деталі"/>

                    
<x-catalog.button wire:click="openPlanForm" wire:loading.attr="disabled" wire:target="openPlanForm" aria-haspopup="dialog" variant="primary" class="catalog-add-button sm:ml-auto">
                        <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
                        Створити
                    </x-catalog.button>
                </div>
            </x-catalog.panel>
            <x-modal name="plan-reports" maxWidth="2xl" focusable>
                <div role="dialog" aria-modal="true" aria-labelledby="plan-reports-title">
                    <div class="flex items-center justify-between border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4">
                        <h3 id="plan-reports-title" class="text-xl font-bold text-[#174a47]">Звіти плану</h3>
                        <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити звіти" class="rounded-md p-2 text-[#245b53] hover:bg-white">✕</button>
                    </div>
                    <div class="grid grid-cols-1 gap-3 p-5 sm:grid-cols-2">
<a class="catalog-button catalog-button-secondary" href="{{ route('plan-tasks.all',['order_name_id'=> $selectedOrder,'sender_department' => $sender_department_id,'receiver_department' => $receiver_department_id]) }}" target="_blank">
                    План у Pdf
                </a><a class="catalog-button catalog-button-secondary" href="{{ route('plan-task.specification.norm',['order_name_id'=> $selectedOrder,'sender_department' => $sender_department_id, 'receiver_department' => $receiver_department_id, 'type_report_in' => 'Pdf', 'with_purchased' => $with_purchased, 'with_material_purchased' => $with_material_purchased]) }}" target="_blank">
                    Специфіковані норми у Pdf
                </a><a class="catalog-button catalog-button-secondary" href="{{ route('plan-task.detail.specification.norm',['order_name_id'=> $selectedOrder,'sender_department' => $sender_department_id, 'receiver_department' => $receiver_department_id, 'type_report_in' => 'Pdf','with_purchased' => $with_purchased, 'with_material_purchased' => $with_material_purchased]) }}" target="_blank">
                    Подетально-специфіковані норми у Pdf
                </a><a class="catalog-button catalog-button-secondary" href="{{ route('plan-task.specification.norm',['order_name_id'=> $selectedOrder,'sender_department' => $sender_department_id, 'receiver_department' => $receiver_department_id, 'type_report_in' => 'Excel','with_purchased' => $with_purchased, 'with_material_purchased' => $with_material_purchased]) }}" target="_blank">
                    Специфіковані норми в Excel
                </a>
                    </div>
                    <div class="flex justify-end border-t border-[#d4e5e0] px-5 py-4"><x-catalog.button x-on:click="$dispatch('close')">Закрити</x-catalog.button></div>
                </div>
            </x-modal>
            <div class="overflow-hidden rounded-lg border border-[#a8c8c5] bg-white shadow-sm"><div class="overflow-x-auto">
                <table class="catalog-table">
                    <thead>
                    <tr>
                        <th scope="col">
                            Матеріал
                        </th>
                        <th scope="col">
                            Деталь
                        </th>
                        <th scope="col">
                            Найменування деталі
                        </th>
                        <th scope="col">
                            Застосовність
                        </th>
                        <th scope="col">
                            Заг. кіл-ть
                        </th>
                        <th scope="col">
                            Замовл.
                        </th>
                        <th scope="col">
                            Цех відправник
                        </th>
                        <th scope="col">
                            Цех отримувач
                        </th>
                        <th scope="col">
                           Додано
                        </th>
                        <th scope="col">
                           З покуп.
                        </th>
                        <th scope="col">Дії</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($items as $item)
                        <tr>
                            <td class="align-middle">

                                    <div>
                                        <input type="checkbox" disabled  name="material" @if($item->material) checked @endif id="exactMatchCheckbox" value="1">
                                    </div>

                            </td>
                            <td class="align-middle">
                                {{ $item->designation->designation??'' }}
                            </td>
                            <td class="align-middle">
                                {{ $item->designation->name??'' }}
                            </td>
                            <td class="align-middle">
                                {{ $item->quantity??'' }}
                            </td>
                            <td class="align-middle">
                                {{ $item->quantity_total??'' }}
                            </td>
                            <td class="align-middle">
                                {{ $item->orderName->name??'' }}
                            </td>
                            <td class="align-middle">
                                {{ $item->senderDepartment->number??'' }}
                            </td>
                            <td class="align-middle">
                                {{ $item->receiverDepartment->number??'' }}
                            </td>
                            <td class="align-middle">
                                @if($item->is_report_application_statement==1)
                                        З відом застосув.
                                    @elseif($item->is_report_application_statement==2)
                                        Зі здаточ.
                                     @endif
                            </td>
                            <td class="align-middle">
                                <div>
                                    <input type="checkbox" disabled  name="with_purchased" @if($item->with_purchased) checked @endif id="exactMatchCheckbox" value="1">
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="flex items-center justify-end gap-2">
                                    <x-catalog.button wire:click="openPlanForm({{ $item->id }})" wire:loading.attr="disabled" wire:target="openPlanForm" aria-haspopup="dialog">Редагувати</x-catalog.button>
                                    <x-catalog.button variant="danger"
                                        wire:key="{{ $item->id }}"
                                        ::disabled="deleting" aria-haspopup="dialog"
                                        data-delete-name="{{ ($item->designation->designation ?? '').' — замовлення '.($item->orderName->name ?? '') }}"
                                        x-on:click="deleteId = {{ $item->id }}; deleteName = $el.dataset.deleteName; deleteError = ''; $dispatch('open-modal', 'delete-plan-task')">
                                        Видалити
                                    </x-catalog.button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table></div>
               <div class="border-t border-[#a8c8c5] px-4 py-4">
                     <p class="mb-3 text-sm text-slate-600">Записів: {{ $items->total() }}</p>
                     {{ $items->appends(request()->input())->links('livewire.pagination.material-stocks') }}
                </div>
            </div>
        </div>
    </div>

    <x-modal-window name="viewLog" title="">
        <x-slot:body>
            <div class="flex justify-center px-5 py-4 text-xl font-bold text-[#174a47]">
                План з відомості застосування
            </div>
            <div class="flex justify-center px-5 py-2 text-base">
                Замовлення &nbsp<b>№{{$order_number}}</b>
            </div>
            <div class="flex justify-center px-5 py-2 text-base">
                З цеху &nbsp<b>{{$sender_department}}</b>
            </div>
            <div class="flex justify-center px-5 py-2 text-base">
                До цеху &nbsp<b>{{$receiver_department}}</b>
            </div>
            <div class="flex justify-end px-5 py-4">
                <x-loading-indicator></x-loading-indicator>
                <button wire:click="makeFromDisassembly"
                        class="catalog-button catalog-button-secondary">
                    Зформувати
                </button>
            </div>
        </x-slot:body>
    </x-modal-window>

    <x-modal-window name="viewOrderFromOrder" title="" width="max-w-lg">
        <x-slot:body>
            <div class="flex justify-center px-5 py-4 text-xl font-bold text-[#174a47]">
                Перенести план
            </div>
            @if (session()->has('error'))
                <div class="mb-4 rounded bg-red-100 border border-red-400 text-red-700 px-4 py-3">
                    {{ session('error') }}
                </div>
            @endif

            @if (session()->has('success'))
                <div class="mb-4 rounded bg-green-100 border border-green-400 text-green-700 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif
            <div class="grid grid-cols-2 gap-4 px-6 py-4">
                <div>
                    <label class="block">
                        <span class="text-gray-700">З замовлення</span>
                        <select
                            wire:model="from_order_id"
                            class="catalog-input">
                            <option value="">—</option>
                            @foreach($order_names as $order)
                                <option value="{{ $order->id }}">
                                    {{ $order->name }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <div>
                    <label class="block">
                        <span class="text-gray-700">На замовлення</span>
                        <select
                            wire:model="to_order_id"
                            class="catalog-input">
                            <option value="">—</option>
                            @foreach($order_names as $order)
                                <option value="{{ $order->id }}">
                                    {{ $order->name }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                </div>
            </div>
            <div class="flex justify-end px-5 py-4">
                <x-loading-indicator></x-loading-indicator>
                <button wire:click="makeFromOrderToOrder"
                        class="catalog-button catalog-button-secondary">
                    Зформувати
                </button>
            </div>
        </x-slot:body>
    </x-modal-window>
    @if (session()->has('error'))
        <div class="mb-4 rounded bg-red-100 border border-red-400 text-red-700 px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('success'))
        <div class="mb-4 rounded bg-green-100 border border-green-400 text-green-700 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif
    <x-modal name="plan-form" maxWidth="2xl" :show="(bool) old('_plan_form') && $errors->any()" focusable>
        <div role="dialog" aria-modal="true" aria-labelledby="plan-form-title">
            <div class="flex items-center justify-between border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4">
                <h3 id="plan-form-title" class="text-xl font-bold text-[#174a47]">{{ $editingPlanTask ? 'Редагувати запис плану' : 'Створити запис плану' }}</h3>
                <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити форму" class="rounded-md p-2 text-[#245b53] hover:bg-white">✕</button>
            </div>
            @if($planFormLoaded)
                <div class="p-5 sm:p-6" wire:key="plan-form-{{ $planFormVersion }}">
                    @include('admin.include.plan-tasks.'.($editingPlanTask ? 'edit' : 'create').'-modal-form', array_merge($planFormData, ['item' => $editingPlanTask]))
                    @if($errors->any())
                        <div role="alert" class="mt-3 text-sm text-red-600">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
                    @endif
                </div>
            @endif
        </div>
    </x-modal>
    <div wire:key="delete-plan-task-dialog">
        <x-catalog.delete-dialog name="delete-plan-task" method="deletePlanTask" />
    </div>
</div>
