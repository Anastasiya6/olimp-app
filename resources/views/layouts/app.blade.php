<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <style>
            /*!
 * Load Awesome v1.1.0 (http://github.danielcardoso.net/load-awesome/)
 * Copyright 2015 Daniel Cardoso <@DanielCardoso>
 * Licensed under MIT
 */
            .la-ball-spin,
            .la-ball-spin > div {
                position: relative;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
            }
            .la-ball-spin {
                display: block;
                font-size: 0;
                color: #fff;
            }
            .la-ball-spin.la-dark {
                color: #333;
            }
            .la-ball-spin > div {
                display: inline-block;
                float: none;
                background-color: currentColor;
                border: 0 solid currentColor;
            }
            .la-ball-spin {
                width: 32px;
                height: 32px;
            }
            .la-ball-spin > div {
                position: absolute;
                top: 50%;
                left: 50%;
                width: 8px;
                height: 8px;
                margin-top: -4px;
                margin-left: -4px;
                border-radius: 100%;
                -webkit-animation: ball-spin 1s infinite ease-in-out;
                -moz-animation: ball-spin 1s infinite ease-in-out;
                -o-animation: ball-spin 1s infinite ease-in-out;
                animation: ball-spin 1s infinite ease-in-out;
            }
            .la-ball-spin > div:nth-child(1) {
                top: 5%;
                left: 50%;
                -webkit-animation-delay: -1.125s;
                -moz-animation-delay: -1.125s;
                -o-animation-delay: -1.125s;
                animation-delay: -1.125s;
            }
            .la-ball-spin > div:nth-child(2) {
                top: 18.1801948466%;
                left: 81.8198051534%;
                -webkit-animation-delay: -1.25s;
                -moz-animation-delay: -1.25s;
                -o-animation-delay: -1.25s;
                animation-delay: -1.25s;
            }
            .la-ball-spin > div:nth-child(3) {
                top: 50%;
                left: 95%;
                -webkit-animation-delay: -1.375s;
                -moz-animation-delay: -1.375s;
                -o-animation-delay: -1.375s;
                animation-delay: -1.375s;
            }
            .la-ball-spin > div:nth-child(4) {
                top: 81.8198051534%;
                left: 81.8198051534%;
                -webkit-animation-delay: -1.5s;
                -moz-animation-delay: -1.5s;
                -o-animation-delay: -1.5s;
                animation-delay: -1.5s;
            }
            .la-ball-spin > div:nth-child(5) {
                top: 94.9999999966%;
                left: 50.0000000005%;
                -webkit-animation-delay: -1.625s;
                -moz-animation-delay: -1.625s;
                -o-animation-delay: -1.625s;
                animation-delay: -1.625s;
            }
            .la-ball-spin > div:nth-child(6) {
                top: 81.8198046966%;
                left: 18.1801949248%;
                -webkit-animation-delay: -1.75s;
                -moz-animation-delay: -1.75s;
                -o-animation-delay: -1.75s;
                animation-delay: -1.75s;
            }
            .la-ball-spin > div:nth-child(7) {
                top: 49.9999750815%;
                left: 5.0000051215%;
                -webkit-animation-delay: -1.875s;
                -moz-animation-delay: -1.875s;
                -o-animation-delay: -1.875s;
                animation-delay: -1.875s;
            }
            .la-ball-spin > div:nth-child(8) {
                top: 18.179464974%;
                left: 18.1803700518%;
                -webkit-animation-delay: -2s;
                -moz-animation-delay: -2s;
                -o-animation-delay: -2s;
                animation-delay: -2s;
            }
            .la-ball-spin.la-sm {
                width: 16px;
                height: 16px;
            }
            .la-ball-spin.la-sm > div {
                width: 4px;
                height: 4px;
                margin-top: -2px;
                margin-left: -2px;
            }
            .la-ball-spin.la-2x {
                width: 64px;
                height: 64px;
            }
            .la-ball-spin.la-2x > div {
                width: 16px;
                height: 16px;
                margin-top: -8px;
                margin-left: -8px;
            }
            .la-ball-spin.la-3x {
                width: 96px;
                height: 96px;
            }
            .la-ball-spin.la-3x > div {
                width: 24px;
                height: 24px;
                margin-top: -12px;
                margin-left: -12px;
            }
            /*
             * Animation
             */
            @-webkit-keyframes ball-spin {
                0%,
                100% {
                    opacity: 1;
                    -webkit-transform: scale(1);
                    transform: scale(1);
                }
                20% {
                    opacity: 1;
                }
                80% {
                    opacity: 0;
                    -webkit-transform: scale(0);
                    transform: scale(0);
                }
            }
            @-moz-keyframes ball-spin {
                0%,
                100% {
                    opacity: 1;
                    -moz-transform: scale(1);
                    transform: scale(1);
                }
                20% {
                    opacity: 1;
                }
                80% {
                    opacity: 0;
                    -moz-transform: scale(0);
                    transform: scale(0);
                }
            }
            @-o-keyframes ball-spin {
                0%,
                100% {
                    opacity: 1;
                    -o-transform: scale(1);
                    transform: scale(1);
                }
                20% {
                    opacity: 1;
                }
                80% {
                    opacity: 0;
                    -o-transform: scale(0);
                    transform: scale(0);
                }
            }
            @keyframes ball-spin {
                0%,
                100% {
                    opacity: 1;
                    -webkit-transform: scale(1);
                    -moz-transform: scale(1);
                    -o-transform: scale(1);
                    transform: scale(1);
                }
                20% {
                    opacity: 1;
                }
                80% {
                    opacity: 0;
                    -webkit-transform: scale(0);
                    -moz-transform: scale(0);
                    -o-transform: scale(0);
                    transform: scale(0);
                }
            }
        </style>
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')
            {{--<div>
                <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-200 text-gray-200">
                    <div :class="sidebarOpen ? 'block' : 'hidden'" @click="sidebarOpen = false" class="fixed z-20 inset-0 bg-black opacity-50 transition-opacity lg:hidden"></div>

                    <div :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'" class="fixed z-30 inset-y-0 left-0 w-64 transition duration-300 transform bg-gray-900 overflow-y-auto lg:translate-x-0 lg:static lg:inset-0">
                        <div class="flex items-center justify-center mt-8">
                            <div class="flex items-center">
                                <svg class="h-12 w-12" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M364.61 390.213C304.625 450.196 207.37 450.196 147.386 390.213C117.394 360.22 102.398 320.911 102.398 281.6C102.398 242.291 117.394 202.981 147.386 172.989C147.386 230.4 153.6 281.6 230.4 307.2C230.4 256 256 102.4 294.4 76.7999C320 128 334.618 142.997 364.608 172.989C394.601 202.981 409.597 242.291 409.597 281.6C409.597 320.911 394.601 360.22 364.61 390.213Z" fill="#4C51BF" stroke="#4C51BF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M201.694 387.105C231.686 417.098 280.312 417.098 310.305 387.105C325.301 372.109 332.8 352.456 332.8 332.8C332.8 313.144 325.301 293.491 310.305 278.495C295.309 263.498 288 256 275.2 230.4C256 243.2 243.201 320 243.201 345.6C201.694 345.6 179.2 332.8 179.2 332.8C179.2 352.456 186.698 372.109 201.694 387.105Z" fill="white"></path>
                                </svg>
                            </div>
                        </div>


                        <nav class="mt-10">
                            <a href="{{ route('specifications.index') }}"
                               class="text-gray-100 flex items-center mt-4 py-2 px-6">

                                <span class="mx-3">М0020</span>
                            </a>

                            <a href="{{ route('materials.index') }}"
                               class="text-gray-100 flex items-center mt-4 py-2 px-6">

                                <span class="mx-3">Материалы</span>
                            </a>
                        </nav>
                    </div>

                    <div class="flex-1 flex flex-col overflow-hidden">
                        <header class="flex justify-between items-center py-4 px-6 bg-white border-b-4 border-indigo-600">
                            @if (isset($header))
                                <header class="bg-white dark:bg-gray-800">
                                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                        {{ $header }}
                                    </div>
                                </header>
                            @endif

                        </header>

                        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                            {{ $slot }}
                        </main>
                    </div>
                </div>
            </div>--}}

            <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-white dark:bg-gray-800 shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
            @endif

            <!-- Page Content -->
                <main>
                    {{ $slot }}
                </main>
         </div>
    </body>
</html>
