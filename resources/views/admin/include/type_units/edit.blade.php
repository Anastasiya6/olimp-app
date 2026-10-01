<x-catalog.page title="Редагувати одиницю вимірювання" subtitle="Змініть дані одиниці вимірювання." width="max-w-5xl">
    <x-catalog.panel>
        @include('admin.include.type_units.form', ['typeUnit' => $item])
    </x-catalog.panel>
</x-catalog.page>
