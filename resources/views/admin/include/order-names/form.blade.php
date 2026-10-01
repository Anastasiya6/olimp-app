@php
    $editing = isset($orderName);
    $prefix = $editing ? 'edit-order-name' : 'create-order-name';
    $restoreInput = !($inModal ?? false) || ($editing ? (string) old('_order_name_edit') === (string) $orderName->id : (bool) old('_order_name_create'));
    $isOrder = $editing ? $orderName->is_order : false;
@endphp
<form method="POST" action="{{ $editing ? route('order-names.update', $orderName->id) : route('order-names.store') }}" class="catalog-form">
    @csrf
    @if($editing)
        @method('PUT')
    @endif
    @if($inModal ?? false)
        <input type="hidden" name="{{ $editing ? '_order_name_edit' : '_order_name_create' }}" value="{{ $editing ? $orderName->id : 1 }}" />
    @endif
    @foreach(['name' => 'Назва або номер замовлення', 'quantity' => 'Кількість комплектів'] as $field => $label)
        <div class="mb-5">
            <label for="{{ $prefix }}-{{ $field }}" class="catalog-label">{{ $label }}</label>
            <input id="{{ $prefix }}-{{ $field }}" type="text" name="{{ $field }}" class="catalog-input"
                value="{{ $restoreInput ? old($field, $editing ? $orderName->{$field} : '') : ($editing ? $orderName->{$field} : '') }}" />
            @if($restoreInput)
                @error($field) <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
            @endif
        </div>
    @endforeach
    <input type="hidden" name="is_order" value="0" />
    <label for="{{ $prefix }}-is-order" class="mb-5 flex items-center gap-2 text-base text-slate-800">
        <input id="{{ $prefix }}-is-order" type="checkbox" name="is_order" value="1" @checked($restoreInput ? old('is_order', $isOrder) : $isOrder)
            class="h-5 w-5 rounded border-[#a8c8c5] text-teal-700 focus:ring-teal-600" />
        Є замовленням
    </label>
    <div class="flex flex-wrap justify-end gap-3 border-t border-[#d4e5e0] pt-5">
        @if($inModal ?? false)
            <x-catalog.button x-on:click="$dispatch('close')">Скасувати</x-catalog.button>
        @else
            <x-catalog.button :href="route('order-names.index')">Скасувати</x-catalog.button>
        @endif
        <x-catalog.button type="submit" variant="primary">{{ $editing ? 'Зберегти зміни' : 'Зберегти замовлення' }}</x-catalog.button>
    </div>
</form>
