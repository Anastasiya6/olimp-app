<x-welcome-layout>
    <x-slot name="header"><h1 class="text-xl font-bold text-[#174a47]">Головна</h1></x-slot>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        @foreach([
            ['public-reports.index', 'Звіти', 'Відомості застосування, норми витрат і цехові списки.', 'M4 19h16M7 15V9m5 6V5m5 10v-4'],
            ['public-specification-logs.index', 'Зміни у специфікації', 'Історія змін складу виробів і деталей.', 'M8 6h12M8 12h12M8 18h12M4 6h.01M4 12h.01M4 18h.01'],
            ['public-designation-material-logs.index', 'Зміни у нормах', 'Перегляд змін норм витрат матеріалів.', 'M12 8v4l3 3M3 12a9 9 0 1 0 3-6M3 3v6h6']
        ] as [$pageRoute, $label, $description, $icon])
            <a href="{{ route($pageRoute) }}" class="group flex flex-col rounded-xl border border-[#bfd8d1] bg-white p-6 shadow-sm transition-colors hover:border-[#8bb5a9] hover:bg-[#f7fbfa] focus:outline-none focus:ring-2 focus:ring-teal-600">
                <span class="mb-5 flex h-11 w-11 items-center justify-center rounded-lg bg-[#e3f1ee] text-[#245b53]"><svg aria-hidden="true" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" /></svg></span>
                <h2 class="text-lg font-bold text-[#174a47]">{{ $label }}</h2>
                <p class="mt-2 mb-5 text-sm leading-relaxed text-slate-600">{{ $description }}</p>
                <span class="mt-auto text-sm font-semibold text-[#245b53]">Відкрити →</span>
            </a>
        @endforeach
    </div>
</x-welcome-layout>
