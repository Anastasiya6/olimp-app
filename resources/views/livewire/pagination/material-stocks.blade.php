<nav aria-label="Сторінки матеріалів" class="flex flex-wrap items-center justify-between gap-3">
    <p class="text-base text-slate-700">
        Показано <span class="font-semibold">{{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }}</span>
        із <span class="font-semibold">{{ $paginator->total() }}</span>
    </p>
    @if($paginator->hasPages())
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')"
                @disabled($paginator->onFirstPage()) aria-label="Попередня сторінка"
                class="min-h-[44px] rounded-md border border-[#a8c8c5] bg-white px-3 py-2 text-base font-semibold text-[#245b53] hover:bg-[#e3f1ee] focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 disabled:cursor-default disabled:opacity-50">
                Назад
            </button>
            <span class="px-2 text-base font-semibold text-slate-800 sm:hidden">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
            <div class="hidden flex-wrap gap-1 sm:flex">
                @foreach($elements as $element)
                    @if(is_string($element))
                        <span class="px-2 py-2 text-base text-slate-600">{{ $element }}</span>
                    @else
                        @foreach($element as $page => $url)
                            @if($page == $paginator->currentPage())
                                <span aria-current="page" aria-label="Сторінка {{ $page }}"
                                    class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-md border border-[#286357] bg-[#286357] px-3 py-2 text-base font-bold text-white">{{ $page }}</span>
                            @else
                                <button type="button" wire:key="stock-page-{{ $page }}" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                    aria-label="Перейти на сторінку {{ $page }}"
                                    class="min-h-[44px] min-w-[44px] rounded-md border border-[#a8c8c5] bg-white px-3 py-2 text-base font-semibold text-[#245b53] hover:bg-[#e3f1ee] focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">{{ $page }}</button>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>
            <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')"
                @disabled(!$paginator->hasMorePages()) aria-label="Наступна сторінка"
                class="min-h-[44px] rounded-md border border-[#a8c8c5] bg-white px-3 py-2 text-base font-semibold text-[#245b53] hover:bg-[#e3f1ee] focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 disabled:cursor-default disabled:opacity-50">
                Далі
            </button>
        </div>
    @endif
</nav>
