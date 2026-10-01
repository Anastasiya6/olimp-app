<x-catalog.page title="Редагувати замовлення" width="max-w-5xl">
    <x-catalog.panel>
        @include('admin.include.order-names.form', ['orderName' => $item])
    </x-catalog.panel>
</x-catalog.page>
