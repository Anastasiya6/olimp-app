<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Редагувати запис') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                {{-- Livewire компонент для редагування існуючого документа --}}
                @livewire('issuance-material-search', ['materialIssuanceId' => $materialIssuanceId])
            </div>
        </div>
    </div>
</x-app-layout>
