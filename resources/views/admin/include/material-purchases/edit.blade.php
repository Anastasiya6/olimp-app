<x-catalog.page title="Редагувати заміну матеріалу" subtitle="Дані заміни матеріалу та пов’язані замовлення." width="max-w-5xl">
    <x-catalog.panel>
        @include('admin.include.material-purchases.form', ['purchase' => $item])
    </x-catalog.panel>
</x-catalog.page>
