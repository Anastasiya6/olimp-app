@props(['title', 'subtitle' => '', 'width' => 'max-w-7xl'])
<x-app-layout>
    <x-slot name="header" compact="true">
        <h2 class="text-xl font-bold leading-tight text-[#174a47]">{{ $title }}</h2>
    </x-slot>
    <div class="bg-[#f2f7f6] py-3 sm:py-4">
        <div class="mx-auto {{ $width }} px-3 sm:px-6 lg:px-8">{{ $slot }}</div>
    </div>
</x-app-layout>
