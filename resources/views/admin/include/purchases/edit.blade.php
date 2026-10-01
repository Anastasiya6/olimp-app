<x-catalog.page title="Редагувати покупну деталь" subtitle="Дані покупної деталі та пов’язані замовлення." width="max-w-5xl">
    <x-catalog.panel>
        @include('admin.include.purchases.form', ['purchase' => $item])
    </x-catalog.panel>
</x-catalog.page>
