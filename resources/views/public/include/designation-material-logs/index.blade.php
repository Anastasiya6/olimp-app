<x-welcome-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold leading-tight text-[#174a47]">
            {{ __($title) }}
        </h2>
    </x-slot>
    <livewire:designation-material-log-list/>
</x-welcome-layout>
