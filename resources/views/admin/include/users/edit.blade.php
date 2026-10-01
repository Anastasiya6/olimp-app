<x-catalog.page title="Редагувати співробітника" subtitle="Змініть дані співробітника." width="max-w-5xl">
    <x-catalog.panel>
        @include('admin.include.users.form', ['employee' => $item])
    </x-catalog.panel>
</x-catalog.page>
