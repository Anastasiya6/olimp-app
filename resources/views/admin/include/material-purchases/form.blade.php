@php
    $editing = isset($purchase);
    $prefix = $editing ? 'edit-material-purchase' : 'create-material-purchase';
    $restoreInput = !($inModal ?? false) || ($editing ? (string) old('_material_purchase_edit') === (string) $purchase->id : (bool) old('_material_purchase_create'));
    $selectedOrders = $editing ? $purchase->order_names->modelKeys() : [];
    if ($restoreInput && session()->hasOldInput()) {
        $selectedOrders = old('orders', []);
    }
@endphp
<form method="POST" action="{{ $editing ? route('material-purchases.update', $purchase->id) : route('material-purchases.store') }}" class="catalog-form material-purchase-form">
    @csrf
    @if($editing)
        @method('PUT')
    @endif
    @if($inModal ?? false)
        <input type="hidden" name="{{ $editing ? '_material_purchase_edit' : '_material_purchase_create' }}" value="{{ $editing ? $purchase->id : 1 }}" />
    @endif
    @if($editing)
        <div class="mb-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
            @foreach(['designation' => ['Куди', $purchase->designation->designation ?? ''], 'designation_entry' => ['Що', $purchase->designationEntry->designation ?? '']] as $field => [$label, $value])
                <div>
                    <label for="{{ $prefix }}-{{ $field }}" class="catalog-label">{{ $label }}</label>
                    <input id="{{ $prefix }}-{{ $field }}" type="text" name="{{ $field }}" readonly value="{{ $value }}" class="catalog-input" />
                </div>
            @endforeach
        </div>
    @else
        <div class="purchase-designation-fields">
        <livewire:designation-search-dropdown designation_hidden="designation_id" designation_title="Куди" designation_name="designation" :restore_input="$restoreInput && session()->hasOldInput()" key="create-material-purchase-parent" />
        <livewire:designation-search-dropdown designation_hidden="designation_entry_id" designation_title="Що" designation_name="designation_entry" :restore_input="$restoreInput && session()->hasOldInput()" key="create-material-purchase-entry" />
        </div>
    @endif
    <div class="purchase-designation-fields material-purchase-selector">
    <livewire:material-search-dropdown
        :material_id="$editing ? $purchase->material_id : null"
        :material_name="$editing ? $purchase->material?->name : null"
        :material_unit="$editing ? $purchase->material?->unit?->unit : null"
        :show_unit="1" :restore_input="$restoreInput && session()->hasOldInput()"
        :key="$prefix.'-material-'.($editing ? $purchase->id.'-'.($editFormVersion ?? 0) : 'new')" />
    </div>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        @foreach(['norm' => 'Кількість', 'code_1c' => 'Код 1С'] as $field => $label)
            <div class="{{ $field === 'purchase' ? 'sm:col-span-2' : '' }}">
                <label for="{{ $prefix }}-{{ $field }}" class="catalog-label">{{ $label }}</label>
                <input id="{{ $prefix }}-{{ $field }}" type="text" name="{{ $field }}" class="catalog-input"
                    value="{{ $restoreInput ? old($field, $editing ? $purchase->{$field} : '') : ($editing ? $purchase->{$field} : '') }}" />
            </div>
        @endforeach
        <div class="sm:col-span-2">
            <x-catalog.order-picker :orders="$order_names" :selected="$selectedOrders" :id="$prefix.'-orders'" />
        </div>
    </div>
    @if($restoreInput && $errors->any())
        <ul role="alert" class="mt-4 list-inside list-disc p-3 text-sm text-red-700">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    @endif
    <div class="mt-6 flex flex-wrap justify-end gap-3 border-t border-[#d4e5e0] pt-5">
        @if($inModal ?? false)
            <x-catalog.button x-on:click="$dispatch('close')">Скасувати</x-catalog.button>
        @else
            <x-catalog.button :href="route('material-purchases.index')">Скасувати</x-catalog.button>
        @endif
        <x-catalog.button type="submit" variant="primary">{{ $editing ? 'Зберегти зміни' : 'Зберегти запис' }}</x-catalog.button>
    </div>
</form>
