<nav class="border-b border-[#b9d8d3] bg-[#f4faf8] shadow-sm" aria-label="Головна навігація">
    <div class="mx-auto grid max-w-7xl items-center justify-items-center gap-2 px-4 py-3 sm:px-6 lg:px-8 xl:grid-cols-[1fr_auto_1fr]">
        <div class="flex flex-wrap items-center justify-center gap-2 xl:col-start-2">
        @foreach(['public-reports.index' => 'Звіти', 'public-specification-logs.index' => 'Зміни у специфікації', 'public-designation-material-logs.index' => 'Зміни у нормах'] as $pageRoute => $label)
            <a href="{{ route($pageRoute) }}" @if(request()->routeIs(str_replace('.index', '.*', $pageRoute))) aria-current="page" @endif
                class="rounded-lg px-3 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-teal-600 {{ request()->routeIs(str_replace('.index', '.*', $pageRoute)) ? 'bg-[#dceee7] text-[#174a47]' : 'text-slate-700 hover:bg-[#e3f1ee]' }}">{{ $label }}</a>
        @endforeach
        </div>
        <a href="{{ route('admin.home') }}" wire:navigate target="_blank" class="catalog-button catalog-reports-button xl:col-start-3 xl:justify-self-end">Робочий кабінет ↗</a>
    </div>
</nav>
