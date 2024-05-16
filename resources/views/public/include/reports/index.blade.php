<x-welcome-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __($title) }}
        </h2>
    </x-slot>
    <livewire:report-table/>
</x-welcome-layout>
<style>
    .underline-link {
        text-decoration: underline;
    }
</style>
