@php
    $editing = isset($note);
    $prefix = $editing ? 'edit-delivery-note' : 'create-delivery-note';
    $restore = $editing ? (string) old('_delivery_note_edit') === (string) $note->id : (bool) old('_delivery_note_create');
    $defaults = $editing ? $note : $last_record;
    $value = fn ($field, $default = '') => $restore ? old($field, $default) : $default;
@endphp
<form method="POST" action="{{ $editing ? route('delivery-notes.update', $note->id) : route('delivery-notes.store') }}" class="catalog-form">
    @csrf
    @if($editing) @method('PUT') @endif
    <input type="hidden" name="{{ $editing ? '_delivery_note_edit' : '_delivery_note_create' }}" value="{{ $editing ? $note->id : 1 }}" />
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="{{ $prefix }}-number" class="catalog-label">Документ №</label>
            <input id="{{ $prefix }}-number" type="text" name="document_number" class="catalog-input" value="{{ $value('document_number', $defaults->document_number) }}" />
        </div>
        <div>
            <label for="{{ $prefix }}-date" class="catalog-label">Дата документа</label>
            <input id="{{ $prefix }}-date" type="date" name="document_date" class="catalog-input" value="{{ $value('document_date', \Carbon\Carbon::parse($defaults->document_date)->format('Y-m-d')) }}" />
        </div>
        <div class="sm:col-span-2">
            @if($editing)
                <label for="{{ $prefix }}-designation" class="catalog-label">Деталь</label>
                <input id="{{ $prefix }}-designation" type="text" name="designation" readonly class="catalog-input" value="{{ $value('designation', $note->designation->designation) }}" />
            @else
                <div class="delivery-note-designation-picker">
                    <livewire:delivery-note-search-dropdown :restore_input="$restore" key="create-delivery-note-designation" />
                </div>
            @endif
        </div>
        <div>
            <label for="{{ $prefix }}-order" class="catalog-label">Замовлення</label>
            <select id="{{ $prefix }}-order" name="order_name_id" class="catalog-input">
                @foreach($order_names as $order_name)
                    <option value="{{ $order_name->id }}" @selected((string) $value('order_name_id', $defaults->order_name_id) === (string) $order_name->id)>{{ $order_name->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="{{ $prefix }}-quantity" class="catalog-label">Кількість</label>
            <input id="{{ $prefix }}-quantity" type="text" name="quantity" class="catalog-input" value="{{ $value('quantity', $editing ? $note->quantity : '') }}" />
        </div>
        @foreach(['sender_department_id' => 'Цех відправник', 'receiver_department_id' => 'Цех отримувач'] as $field => $label)
            <div>
                <label for="{{ $prefix }}-{{ $field }}" class="catalog-label">{{ $label }}</label>
                <select id="{{ $prefix }}-{{ $field }}" name="{{ $field }}" class="catalog-input">
                    @foreach($form_departments as $department)
                        <option value="{{ $department->id }}" @selected((string) $value($field, $defaults->{$field}) === (string) $department->id)>{{ $department->number }}</option>
                    @endforeach
                </select>
            </div>
        @endforeach
    </div>
    <div class="my-5 flex flex-wrap gap-5">
        @foreach(['with_purchased' => 'З покупними', 'with_material_purchased' => 'Заміна матеріалів'] as $field => $label)
            <label class="flex items-center gap-2">
                <input type="hidden" name="{{ $field }}" value="0" />
                <input type="checkbox" name="{{ $field }}" value="1" @checked($value($field, $editing ? $note->{$field} : false)) class="rounded border-[#a8c8c5] text-teal-700 focus:ring-teal-600" />
                {{ $label }}
            </label>
        @endforeach
    </div>
    @if($restore && $errors->any())
        <div role="alert" class="mb-4 text-sm text-red-600">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif
    <div class="flex flex-wrap justify-end gap-3 border-t border-[#d4e5e0] pt-5">
        <x-catalog.button x-on:click="$dispatch('close')">Скасувати</x-catalog.button>
        <x-catalog.button type="submit" variant="primary">{{ $editing ? 'Зберегти зміни' : 'Зберегти' }}</x-catalog.button>
    </div>
</form>
