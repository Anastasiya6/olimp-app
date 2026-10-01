<x-catalog.page title="Редагувати виріб" subtitle="Змініть номер, назву, маршрут або код 1С." width="max-w-5xl">
    <x-catalog.panel>
        @include('admin.include.designations.form', ['designation' => $item])
    </x-catalog.panel>
</x-catalog.page>
