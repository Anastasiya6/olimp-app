<x-catalog.page title="Редагувати матеріалокомплект" subtitle="Змініть норму витрат матеріалу." width="max-w-5xl">
    <x-catalog.panel>
        @include('admin.include.group-materials.form', ['groupMaterial' => $item])
    </x-catalog.panel>
</x-catalog.page>
