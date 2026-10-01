<x-catalog.page title="Редагувати ПИ0" subtitle="Змініть номер, назву, ГОСТ або код 1С." width="max-w-5xl">
    <x-catalog.panel>
        @include('admin.include.pi0s.form', ['designation' => $item])
    </x-catalog.panel>
</x-catalog.page>
