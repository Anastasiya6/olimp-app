@php
    $navigation = [
        ['label' => 'Довідники', 'items' => [
            ['label' => 'Цеха', 'route' => 'departments.index'],
            ['label' => 'Ділянки', 'route' => 'sections.index'],
            ['label' => 'Од. вимірювання', 'route' => 'type_units.index'],
            ['label' => 'Коефіцієнти', 'route' => 'material_coefficients.index'],
            ['label' => 'Співробітники', 'route' => 'users.index'],
        ]],
        ['label' => 'Замовлення', 'route' => 'order-names.index'],
        ['label' => 'Розузловання', 'route' => 'orders.index'],
        ['label' => 'Вироби', 'items' => [
            ['label' => 'Вироби', 'route' => 'designations.index'],
            ['label' => 'ПИ0', 'route' => 'pi0s.index'],
            ['label' => 'Матеріалокомплекти', 'route' => 'group-materials.index'],
            ['label' => 'M0020', 'route' => 'specifications.index'],
        ]],
        ['label' => 'Матеріали', 'items' => [
            ['label' => 'Норми', 'route' => 'designation-materials.index'],
            ['label' => 'Матеріали', 'route' => 'materials.index'],
            ['label' => 'Матеріали з 1С', 'route' => 'import-material-stocks.index'],
            ['label' => 'Видача матеріалів', 'route' => 'issuance-materials.index'],
            ['label' => 'Видача матеріалів без норм', 'route' => 'manual-issuance-materials.index'],
            ['label' => 'Пошук деталі в плані', 'route' => 'search-designation-in-plan.index'],
        ]],
        ['label' => 'Здаточні', 'items' => [
            ['label' => 'Здаточні', 'route' => 'delivery-notes.index'],
            ['label' => 'Списання', 'route' => 'write-offs.index'],
            ['label' => 'Покупні деталі в здаточних', 'route' => 'purchases.index'],
            ['label' => 'Заміна матеріалів в здаточних', 'route' => 'material-purchases.index'],
            ['label' => 'План', 'route' => 'plan-tasks.index'],
        ]],
        ['label' => 'Завдання', 'items' => [
            ['label' => 'Завдання цех', 'route' => 'tasks.index', 'parameters' => ['type' => 'department']],
            ['label' => 'Завдання технолог', 'route' => 'tasks.index', 'parameters' => ['type' => 'technologist']],
        ]],
        ['label' => 'Звіти', 'items' => [
            ['label' => 'Звіти', 'route' => 'reports.index'],
            ['label' => 'Зміни у специфікації', 'route' => 'specification-logs.index'],
            ['label' => 'Зміни у нормах', 'route' => 'designation-material-logs.index'],
        ]],
    ];
    $isNavigationActive = function ($item) {
        return request()->routeIs(str_replace('.index', '.*', $item['route']))
            && (!isset($item['parameters']['type']) || request()->route('type') === $item['parameters']['type']);
    };
    $navigationButton = 'inline-flex min-h-[44px] items-center justify-between gap-2 rounded-lg border px-3 py-2 text-base font-semibold transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2';
@endphp

<nav x-data="{ mobileOpen: false }" aria-label="Головна навігація"
    class="relative z-40 border-b-2 border-[#b9d8d3] bg-[#f4faf8] shadow-sm">
    <div class="mx-auto max-w-7xl px-4 py-3 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif
                class="inline-flex min-h-[44px] shrink-0 items-center gap-2 rounded-lg border border-[#c4dcd7] bg-white px-3 py-2 text-base font-bold text-[#245b53] shadow-sm hover:bg-[#e3f1ee] focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m3 10 9-7 9 7M5 9v11h5v-6h4v6h5V9" />
                </svg>
                На головну
            </a>
            <button type="button" x-on:click="mobileOpen = !mobileOpen" :aria-expanded="mobileOpen.toString()"
                aria-controls="main-navigation-links"
                class="ml-auto inline-flex min-h-[44px] items-center gap-2 rounded-lg border border-[#b9d8d3] bg-white px-4 py-2 text-base font-semibold text-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 lg:hidden">
                <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                Меню
            </button>
            <div id="main-navigation-links" :class="mobileOpen ? 'flex' : 'hidden'"
                class="hidden w-full flex-col gap-1 border-t border-[#d4e5e0] pt-3 lg:flex lg:w-auto lg:flex-1 lg:flex-row lg:flex-wrap lg:items-center lg:justify-end lg:border-0 lg:pt-0">
                @foreach($navigation as $index => $section)
                    @php
                        $sectionActive = isset($section['items'])
                            ? collect($section['items'])->contains(fn ($item) => $isNavigationActive($item))
                            : $isNavigationActive($section);
                        $sectionStyle = $sectionActive
                            ? 'border-[#9fc6bd] bg-[#dceee7] text-[#194b41]'
                            : 'border-transparent text-slate-800 hover:border-[#c4dcd7] hover:bg-[#e7f2ef]';
                    @endphp
                    @if(isset($section['items']))
                        <div class="relative" x-data="{ expanded: false }"
                            x-on:click.outside="expanded = false"
                            x-on:keydown.escape.stop="if (expanded) { expanded = false; $refs.trigger.focus(); }">
                            <button type="button" x-ref="trigger" x-on:click="expanded = !expanded"
                                :aria-expanded="expanded.toString()" aria-controls="main-navigation-section-{{ $index }}"
                                class="{{ $navigationButton }} {{ $sectionStyle }} w-full lg:w-auto">
                                {{ $section['label'] }}
                                <svg aria-hidden="true" class="h-4 w-4 shrink-0 transition-transform" :class="expanded ? 'rotate-180' : ''"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                                </svg>
                            </button>
                            <div id="main-navigation-section-{{ $index }}" x-show="expanded" style="display: none;"
                                x-transition.opacity.duration.150ms
                                class="z-50 mt-2 w-full rounded-xl border border-[#bfd8d1] bg-white p-2 shadow-lg lg:absolute lg:right-0 lg:w-80">
                                @foreach($section['items'] as $link)
                                    @php($linkActive = $isNavigationActive($link))
                                    <a wire:navigate href="{{ route($link['route'], $link['parameters'] ?? []) }}"
                                        x-on:click="expanded = false; mobileOpen = false"
                                        @if($linkActive) aria-current="page" @endif
                                        class="flex min-h-[44px] items-center gap-3 rounded-lg px-3 py-3 text-base font-medium leading-snug focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 {{ $linkActive ? 'bg-[#e3f1ec] text-[#194b41] font-semibold' : 'text-slate-800 hover:bg-[#eef7f8]' }}">
                                        <span aria-hidden="true" class="h-2 w-2 shrink-0 rounded-full {{ $linkActive ? 'bg-teal-700' : 'bg-[#b9d8d3]' }}"></span>
                                        {{ $link['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ route($section['route']) }}" @if($sectionActive) aria-current="page" @endif
                            class="{{ $navigationButton }} {{ $sectionStyle }}">
                            {{ $section['label'] }}
                        </a>
                    @endif
                @endforeach
                <div class="mt-2 flex flex-wrap gap-2 border-t border-[#d4e5e0] pt-3 lg:hidden">
                    <a href="{{ route('dashboard') }}" class="rounded-md px-3 py-2 text-base text-slate-800 hover:bg-[#e3f1ee]">Dashboard</a>
                    <a href="{{ route('profile.edit') }}" class="rounded-md px-3 py-2 text-base text-slate-800 hover:bg-[#e3f1ee]">Профіль</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-md px-3 py-2 text-base text-slate-800 hover:bg-[#e3f1ee]">Вийти</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
