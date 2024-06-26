<nav class="bg-gray-800">
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
        <div class="relative flex h-16 items-center justify-between">
            <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
                <div class="sm:ml-6 sm:block">
                    <div class="flex space-x-4">
                        <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->

                        <a href="{{route('public-reports.index')}}" class="text-gray-300 rounded-md px-3 py-2 text-sm font-medium {{ request()->is('public-reports*') ? 'text-white' : 'hover:bg-gray-700 hover:text-white' }}" aria-current="page">Звіти</a>
                        <a href="{{route('public-specification-logs.index')}}" class="text-gray-300  rounded-md px-3 py-2 text-sm font-medium {{ request()->is('public-specification-logs*') ? 'text-white' : 'hover:bg-gray-700 hover:text-white' }}">Зміни у специфікації</a>
                        <a href="{{route('public-designation-material-logs.index')}}" class="text-gray-300 rounded-md px-3 py-2 text-sm font-medium {{ request()->is('public-designation-material-logs*') ? 'text-white' : 'hover:bg-gray-700 hover:text-white' }}">Зміни у нормах</a>
                    </div>
                </div>
            </div>

            <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                <div class="flex">
                    <div class="sm:flex sm:items-center sm:ms-6">

                        <a href="{{route('admin.home')}}" wire:navigate class="user__menu-admin" title="Administrator panel" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                <circle cx="10.5" cy="10.5" r="9.5" stroke="#00CD3C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15.7427 9.59779L14.0868 7.94188V6.02904C14.0868 5.68317 13.8065 5.40287 13.4601 5.40287C13.1146 5.40287 12.8343 5.68317 12.8343 6.02904V6.68939L11.6013 5.4564C10.9917 4.84714 9.93188 4.84822 9.32369 5.4575L5.18331 9.59779C4.9389 9.84269 4.9389 10.239 5.18331 10.4835C5.42784 10.7283 5.82488 10.7283 6.06931 10.4835L10.2093 6.34308C10.3442 6.20892 10.5819 6.20892 10.716 6.34268L14.8567 10.4835C14.9795 10.6059 15.1396 10.6668 15.2996 10.6668C15.46 10.6668 15.6203 10.6058 15.7427 10.4835C15.9872 10.239 15.9872 9.84271 15.7427 9.59779Z" fill="#00CD3C"/>
                                <path d="M10.6806 7.5317C10.5603 7.41147 10.3655 7.41147 10.2456 7.5317L6.60371 11.1725C6.54621 11.23 6.51361 11.3084 6.51361 11.3903V14.0458C6.51361 14.6689 7.01885 15.1741 7.64195 15.1741H9.44505V12.3817H11.4807V15.1741H13.2838C13.9069 15.1741 14.4122 14.6689 14.4122 14.0458V11.3903C14.4122 11.3084 14.3799 11.23 14.3221 11.1725L10.6806 7.5317Z" fill="#00CD3C"/>
                            </svg>
                        </a></div>
                </div>

            </div>
        </div>
    </div>


    <!-- Mobile menu, show/hide based on menu state. -->
    <div class="sm:hidden" id="mobile-menu">
        <div class="space-y-1 px-2 pb-3 pt-2">
            <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
            <a href="{{route('public-reports.index')}}" class="text-gray-300 rounded-md px-3 py-2 text-sm font-medium {{ request()->is('public-reports*') ? 'text-white' : 'hover:bg-gray-700 hover:text-white' }}" aria-current="page">Звіти</a>
            <a href="{{route('public-specification-logs.index')}}" class="text-gray-300  rounded-md px-3 py-2 text-sm font-medium {{ request()->is('public-specification-logs*') ? 'text-white' : 'hover:bg-gray-700 hover:text-white' }}">Зміни у специфікації</a>
            <a href="{{route('public-designation-material-logs.index')}}" class="text-gray-300 rounded-md px-3 py-2 text-sm font-medium {{ request()->is('public-designation-material-logs*') ? 'text-white' : 'hover:bg-gray-700 hover:text-white' }}">Зміни у нормах</a>
        </div>
    </div>
</nav>

