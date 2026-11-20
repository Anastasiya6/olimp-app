<x-catalog.page title="Редагувати ділянку" subtitle="Змініть дані ділянки." width="max-w-5xl">
    <x-catalog.panel>
        @include('admin.include.sections.form', ['section' => $item])
    </x-catalog.panel>
</x-catalog.page>
