<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __($title) }}
        </h2>
    </x-slot>
    <livewire:admin-specification-log-list :detail-name="$detailName" />
</x-app-layout>
