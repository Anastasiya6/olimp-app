<x-catalog.page title="Редагувати коефіцієнт" subtitle="Змініть дані коефіцієнта." width="max-w-5xl">
    <x-catalog.panel>
        @include('admin.include.material_coefficients.form', ['materialCoefficient' => $item])
    </x-catalog.panel>
</x-catalog.page>
