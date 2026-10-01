<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Олімп') }}</title>

        <!-- Fonts -->

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        

    </head>
    @stack('scripts') <!-- Обов’язково -->
    <body class="min-h-screen bg-[#f2f7f6] font-sans antialiased text-slate-900">

        @if(session('success'))
            <x-layout>
                <x-confirmation-modal>
                    <x-slot name="title">
                        Формування відомості успішно виконано!
                    </x-slot>

                    <x-slot name="body">
                    </x-slot>

                    <x-slot name="footer">
                        <x-button class="bg-blue-400 hover:bg-blue-500">Close</x-button>
                    </x-slot>
                </x-confirmation-modal>
            </x-layout>
        @endif

        @include('layouts.navigation-public')
        @isset($header)
            <header class="border-b border-[#d4e5e0] bg-white">
                <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">{{ $header }}</div>
            </header>
        @endisset
        <!-- Page Content -->
        <main>
            <div class="max-w-7xl mx-auto px-4 py-5 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </body>
</html>
