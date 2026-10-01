<x-catalog.page title="Редагувати цех" subtitle="Змініть номер цеху." width="max-w-5xl">
    <x-catalog.panel>
        @include('admin.include.departments.form', ['department' => $item])
    </x-catalog.panel>
</x-catalog.page>
