<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold leading-tight text-[#174a47]">Редагувати матеріал</h2>
    </x-slot>
    <div class="bg-[#f2f7f6] py-6 sm:py-8">
        <div class="mx-auto max-w-2xl px-3 sm:px-6">
            <div class="overflow-hidden rounded-lg border border-[#bfd8d1] bg-white shadow-sm">
                <div class="border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4 sm:px-6">
                    <h3 class="text-lg font-bold text-[#174a47]">Дані матеріалу</h3>
                    <p class="mt-1 text-base text-slate-600">Змініть дані та збережіть матеріал.</p>
                </div>
                <div class="p-5 sm:p-6">
                    @include('admin.include.materials.edit-form', ['inModal' => false])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
