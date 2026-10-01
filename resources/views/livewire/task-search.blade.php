<div class="tasks-page" x-data="{ deleteId: null, deleteName: '', deleting: false, deleteError: '' }" x-on:task-form-open.window="$dispatch('open-modal', 'task-form')">

    <div class="min-w-full align-middle">
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-[#bfd8d1] bg-white p-4 mb-4">
            <div class="flex flex-wrap items-center gap-3">
                <label class="inline-flex items-center" for="task-department">Цех отрим.</label>

                <select wire:model.change="selectedDepartmentSender" wire:change="updateSearch"  id="task-department"
                        class="h-9 w-44 rounded-md border-slate-300 py-1.5 text-sm focus:border-teal-600 focus:ring-teal-600">
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}"
                            @if($department->id==$default_first_department) selected @endif 'selected'>
                            {{ $department->number }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button wire:click="viewConfirm" class="catalog-button catalog-reports-button">
                    <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Zm0 0v6h6M8 17v-3m4 3v-6m4 6v-2" /></svg>
                    Сформувати звіт
                </button>
            </div>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-[#bfd8d1] bg-white p-4 mb-4">
            <button type="button"
                    class="catalog-button catalog-button-danger"
                    :disabled="deleting" aria-haspopup="dialog"
                    data-delete-name="{{ 'Цех '.($departments->firstWhere('id', $selectedDepartmentSender)?->number ?? '').' — '.($type === 'department' ? 'завдання цеху' : 'завдання технолога') }}"
                    x-on:click="deleteId = {{ (int) $selectedDepartmentSender }}; deleteName = $el.dataset.deleteName; deleteError = ''; $dispatch('open-modal', 'delete-all-tasks')">
                Видалити деталі
            </button>
                            <x-catalog.button wire:click="openTaskForm" wire:loading.attr="disabled" wire:target="openTaskForm" aria-haspopup="dialog" variant="primary" class="catalog-add-button">
                    <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>Створити
                </x-catalog.button>
            
        </div>
        <div class="min-w-full align-middle">
            <div class="overflow-hidden rounded-lg border border-[#a8c8c5] bg-white shadow-sm"><div class="overflow-x-auto"><table class="catalog-table task-list-table">
                <thead>
                <tr>
                    <th scope="col" class="task-checkbox-column">
                        На обр.
                    </th>
                    <th scope="col">
                        Деталь
                    </th>
                    <th scope="col">
                        Кількість
                    </th>
                    <th scope="col">
                        Відділ
                    </th>
                    <th scope="col" class="task-checkbox-column">
                        Матеріал
                    </th>
                    <th scope="col" class="task-actions-column">Дії</th>
                </tr>
                </thead>

                <tbody>

                @foreach($items as $item)
                    <tr>
                        <td wire:key="{{ $item->id }}" class="align-middle">
                            <div>
                                <input type="checkbox" value="{{$item->id}}" wire:model="selectedItems" wire:key="{{ $item->id }}"/>
                            </div>
                        </td>
                        <td class="align-middle">
                            {{ $item->designation->designation??'' }}
                        </td>
                        <td class="align-middle">
                            {{ $item->quantity??'' }}
                        </td>
                        <td class="align-middle">
                            {{ $item->department->number??'' }}
                        </td>
                        <td class="align-middle">
                            
                                <div>
                                    <input type="checkbox" disabled  name="material" @if($item->material==1) checked @endif id="exactMatchCheckbox" value="1">
                                </div>
                            
                        </td>
                        <td class="align-middle task-actions-column">
                            <div class="flex items-center gap-2">
                                                            <x-catalog.button wire:click="openTaskForm({{ $item->id }})" wire:loading.attr="disabled" wire:target="openTaskForm" aria-haspopup="dialog">Редагувати</x-catalog.button>
                            
                            <x-catalog.button variant="danger"
                                wire:key="{{ $item->id }}"
                                ::disabled="deleting" aria-haspopup="dialog"
                                data-delete-name="{{ ($item->designation->designation ?? '').' — цех '.($item->department->number ?? '') }}"
                                x-on:click="deleteId = {{ $item->id }}; deleteName = $el.dataset.deleteName; deleteError = ''; $dispatch('open-modal', 'delete-task')">
                                Видалити
                            </x-catalog.button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table></div></div>
        </div>
    </div>
    <x-modal-window name="viewLog" title="Звіти завдання" width="max-w-5xl">
        <x-slot:body>





            <div class="grid grid-cols-1 gap-3 py-3 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Форма для "Подет.-специфіковані" -->
                <form action="{{ route('report.task.detail') }}" method="POST" target="_blank">
                    @csrf
                    @foreach($selectedDetails as $item)
                        <input type="hidden" name="ids[]" value="{{ $item->id }}">
                    @endforeach
                    <input type="hidden" name="sender_department" value="{{ $selectedDepartmentSender }}">
                    <input type="hidden" name="without_coefficient" value="{{$without_coefficient}}">
                    <input type="hidden" name="type_report" value="0">
                    <button type="submit" class="catalog-button catalog-button-secondary">
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
                    <button type="submit" class="catalog-button catalog-button-secondary">
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
                    <button type="submit" class="catalog-button catalog-button-secondary">
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
                    <button type="submit" class="catalog-button catalog-button-secondary">
                        Нема матеріалів
                    </button>
                </form>
            </div>
            <div class="w-full">

                <div class="overflow-hidden rounded-lg border border-[#a8c8c5] bg-white shadow-sm"><div class="overflow-x-auto"><table class="catalog-table">
                    <thead>
                    <tr>
                        <th scope="col">
                            Дата внесення
                        </th>
                        <th scope="col">
                            Деталь
                        </th>
                        <th scope="col">
                            Кількість
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    @if($selectedDetails)
                        @foreach($selectedDetails as $item)
                            <tr>
                                <td class="align-middle">
                                    {{\Carbon\Carbon::parse($item->created_at)->format('d.m.Y')}}
                                </td>
                                <td class="align-middle">
                                    {!! $item->designation->designation !!}
                                </td>
                                <td class="align-middle">
                                    {!! $item->quantity !!}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table></div></div>
            </div>

            <!-- Остальная часть модального окна -->

        </x-slot:body>
    </x-modal-window>
        <x-modal name="task-form" maxWidth="2xl" :show="(bool) old('_task_form') && $errors->any()" focusable>
            <div role="dialog" aria-modal="true" aria-labelledby="task-form-title">
                <div class="flex items-center justify-between border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4">
                    <h3 id="task-form-title" class="text-xl font-bold text-[#174a47]">{{ ($editingTask ? 'Редагувати завдання ' : 'Створити завдання ').($type === 'department' ? 'цеху' : 'технолога') }}</h3>
                    <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити форму" class="rounded-md p-2 text-[#245b53] hover:bg-white">✕</button>
                </div>
                @if($taskFormLoaded)
                    <div class="p-5 sm:p-6" wire:key="task-form-{{ $taskFormVersion }}">
                        @include('admin.include.tasks.'.($editingTask ? 'edit' : 'create').'-modal-form', ['item' => $editingTask, 'sender_department_id' => $formDepartmentId, 'sender_department' => $formDepartmentNumber])
                        @if($errors->any())<div role="alert" class="mt-3 text-sm text-red-600">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
                    </div>
                @endif
            </div>
        </x-modal>
    <div wire:key="delete-task-dialog">
        <x-catalog.delete-dialog name="delete-task" method="deleteTask" />
    </div>
    <div wire:key="delete-all-tasks-dialog">
        <x-catalog.delete-dialog name="delete-all-tasks" method="deleteAllTask" title="Видалити всі деталі?"
            description="Усі деталі вибраного цеху в цьому розділі завдань буде видалено. Цю дію неможливо скасувати." />
    </div>
</div>
