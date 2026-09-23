<div x-data
    x-on:stock-in-open.window="$dispatch('open-modal', 'stock-in-import')"
    x-on:stock-in-close.window="$dispatch('close-modal', 'stock-in-import')"
    x-on:opening-stock-open.window="$dispatch('open-modal', 'opening-stock-import')"
    x-on:opening-stock-confirm.window="$dispatch('close-modal', 'opening-stock-import'); $dispatch('open-modal', 'opening-stock-confirmation')"
    x-on:opening-stock-close.window="$dispatch('close-modal', 'opening-stock-import'); $dispatch('close-modal', 'opening-stock-confirmation')">
    <div class="mb-5 rounded-lg border border-gray-200 bg-gray-50 p-4">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="w-full lg:max-w-sm">
                <label for="stock-article-search" class="mb-2 block text-sm font-medium text-gray-700">Пошук за артикулом</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <circle cx="10.5" cy="10.5" r="6.5" />
                            <path stroke-linecap="round" d="m16 16 4 4" />
                        </svg>
                    </span>
                    <input id="stock-article-search" type="search" wire:model.live="searchTerm" wire:keydown="updateSearch"
                        placeholder="Введіть артикул"
                        class="block h-11 w-full rounded-md border-gray-300 bg-white pl-10 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                </div>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <button type="button" wire:click="viewStockIn"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-md border border-gray-800 bg-gray-800 px-4 text-sm font-medium text-white shadow-sm transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m-4-4 4 4 4-4M4 16v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4" />
                    </svg>
                    Вигрузити приход
                </button>
                <button type="button" wire:click="viewStock"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-4 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m3 7 9-4 9 4-9 4-9-4Zm0 0v10l9 4 9-4V7M12 11v10" />
                    </svg>
                    Вигрузити залишки з 1С
                </button>
            </div>
        </div>
        @if(session()->has('message'))
            <p role="status" class="mt-3 text-sm text-gray-600">{{ session('message') }}</p>
        @endif
    </div>

    @if(session()->has('success'))
        <div role="status" class="my-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    <div class="overflow-x-auto rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200 bg-white">
            <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Код 1С
                </th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Артикул
                </th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Назва
                </th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Кількість
                </th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Од.виміру
                </th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Номер приходу
                </th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Дата приходу
                </th>
                <th class="px-4 py-3"></th>
            </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
            @foreach($items as $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                        {{ $item->materials->code ?? '' }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                        {{ $item->materials->article ?? '' }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                        {{ $item->materials->name ?? '' }}
                    </td>
{{--                    <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">--}}
{{--                        {{ \Carbon\Carbon::parse($item->document_date)->format('d.m.Y') ?? '' }}--}}
{{--                    </td>--}}
                    <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                        {{ $item->amount ?? '' }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                        {{ $item->materials->unit->unit ?? '' }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                        {{ $item->document_number ?? '' }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap font-bold text-gray-900">
                        {{ $item->document_date ? \Carbon\Carbon::parse($item->document_date)->format('d.m.Y') : '' }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="py-4">
            {{ $items->appends(request()->input())->links() }}
        </div>
    </div>

    <div wire:key="opening-stock-dialog">
        <x-modal name="opening-stock-import" maxWidth="lg" focusable>
            <div class="p-6" role="dialog" aria-modal="true" aria-labelledby="opening-stock-title">
                <div class="mb-5 flex items-start justify-between gap-4 border-b border-gray-200 pb-4">
                    <div>
                        <h3 id="opening-stock-title" class="text-lg font-semibold text-gray-800">Імпорт залишків з 1С</h3>
                        <p class="mt-1 text-sm text-gray-500">Оберіть Excel-файл із залишками матеріалів.</p>
                    </div>
                    <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити імпорт залишків"
                        class="rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form wire:submit="confirmStock" class="space-y-5">
                    <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-4">
                        <label for="opening-stock-file" class="mb-2 block text-sm font-medium text-gray-700">Файл залишків</label>
                        <input id="opening-stock-file" type="file" wire:model="file" accept=".xlsx,.xls"
                            class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-white file:px-3 file:py-2 file:text-sm file:font-medium file:text-gray-700" />
                        <p class="mt-2 text-xs text-gray-500">Формати: Excel (.xlsx, .xls).</p>
                        <p wire:loading wire:target="file" class="mt-2 text-sm text-gray-600">Завантаження файлу…</p>
                        @error('file') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex justify-end gap-3 border-t border-gray-200 pt-4">
                        <button type="button" x-on:click="$dispatch('close')"
                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Скасувати</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="file,confirmStock"
                            class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 disabled:opacity-50">Продовжити</button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>

    <div wire:key="opening-stock-confirmation-dialog">
        <x-modal name="opening-stock-confirmation" maxWidth="md" focusable>
            <div class="p-6" role="dialog" aria-modal="true" aria-labelledby="opening-stock-confirmation-title">
                <div class="mb-5 flex items-start justify-between gap-4 border-b border-gray-200 pb-4">
                    <h3 id="opening-stock-confirmation-title" class="text-lg font-semibold text-gray-800">Підтвердьте імпорт залишків</h3>
                    <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити підтвердження"
                        wire:loading.attr="disabled" wire:target="unloadingStock"
                        class="rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-700 disabled:opacity-50">
                        <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <p class="break-words text-sm text-gray-700">Файл: {{ $file?->getClientOriginalName() }}</p>
                <p class="mt-3 text-sm leading-relaxed text-gray-600">Поточні матеріали, залишки та документи видачі буде видалено перед імпортом залишків із файлу.</p>
                @error('file') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                <div class="mt-5 flex justify-end gap-3 border-t border-gray-200 pt-4">
                    <button type="button" x-on:click="$dispatch('close')" wire:loading.attr="disabled" wire:target="unloadingStock"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 disabled:opacity-50">Скасувати</button>
                    <button type="button" wire:click="unloadingStock" wire:loading.attr="disabled" wire:target="unloadingStock"
                        class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 disabled:opacity-50">
                        <span wire:loading.remove wire:target="unloadingStock">Імпортувати залишки</span>
                        <span wire:loading wire:target="unloadingStock">Імпортування…</span>
                    </button>
                </div>
            </div>
        </x-modal>
    </div>

{{--**********************************************--}}
    <div wire:key="stock-in-import-dialog">
    <x-modal name="stock-in-import" maxWidth="lg" focusable>
        <div class="p-6" role="dialog" aria-modal="true" aria-labelledby="stock-in-title">
            <div class="mb-5 flex items-start justify-between gap-4 border-b border-gray-200 pb-4">
                <div>
                    <h3 id="stock-in-title" class="text-lg font-semibold text-gray-800">Імпорт приходу з 1С</h3>
                    <p class="mt-1 text-sm text-gray-500">Завантажте файл і виберіть цех, для якого додати прихід.</p>
                </div>
                <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити"
                    wire:loading.attr="disabled" wire:target="unloadingStockIn"
                    class="rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-700">
                    <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            @if(!$confirmingStockIn)
                <form wire:submit="confirmStockIn" class="space-y-5">
                    <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-4">
                        <label for="stock-in-file" class="mb-2 block text-sm font-medium text-gray-700">Файл приходу</label>
                        <input id="stock-in-file" type="file" wire:model="file" accept=".xlsx,.xls"
                            class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-white file:px-3 file:py-2 file:text-sm file:font-medium file:text-gray-700" />
                        <p class="mt-2 text-xs text-gray-500">Формати: Excel (.xlsx, .xls).</p>
                        <p wire:loading wire:target="file" class="mt-2 text-sm text-gray-600">Завантаження файлу…</p>
                        @error('file') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="stock-in-department" class="mb-2 block text-sm font-medium text-gray-700">Цех</label>
                        <select id="stock-in-department" wire:model="stockInDepartmentId" required
                            class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Оберіть цех</option>
                            @foreach($stockInDepartments as $department)
                                <option value="{{ $department->id }}">Цех {{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('stockInDepartmentId') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-2 text-xs leading-relaxed text-gray-500">Імпортуються лише рядки вибраного цеху із секції «Кому:». Інші цехи у файлі пропускаються.</p>
                    </div>
                    <div class="flex justify-end gap-3 border-t border-gray-200 pt-4">
                        <button type="button" x-on:click="$dispatch('close')"
                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Скасувати</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="file,confirmStockIn"
                            class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 disabled:opacity-50">Продовжити</button>
                    </div>
                </form>
            @else
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700">
                    <p class="font-medium">Підтвердьте імпорт приходу</p>
                    <p class="mt-3 break-words">Файл: {{ $file?->getClientOriginalName() }}</p>
                    <p class="mt-2">Цех: <strong>{{ $stockInDepartments->firstWhere('id', $stockInDepartmentId)?->name }}</strong></p>
                    <p class="mt-3 text-gray-500">Буде додано прихід лише для цього цеху.</p>
                </div>
                @error('file') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                @error('stockInDepartmentId') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                <div class="mt-5 flex justify-end gap-3 border-t border-gray-200 pt-4">
                    <button type="button" wire:click="$set('confirmingStockIn', false)" wire:loading.attr="disabled" wire:target="unloadingStockIn"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 disabled:opacity-50">Назад</button>
                    <button type="button" wire:click="unloadingStockIn" wire:loading.attr="disabled" wire:target="unloadingStockIn"
                        class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 disabled:opacity-50">
                        <span wire:loading.remove wire:target="unloadingStockIn">Імпортувати прихід</span>
                        <span wire:loading wire:target="unloadingStockIn">Імпортування…</span>
                    </button>
                </div>
            @endif
        </div>
    </x-modal>
    </div>
    <livewire:view-material-conflict wire:key="conflict-modal" />
{{--    <x-small-modal-window name="viewMaterialConflict" title="">--}}
{{--        <x-slot:body>--}}
{{--            <div class="min-w-full align-middle">--}}
{{--                <table class="min-w-full border divide-y divide-gray-200">--}}
{{--                    <thead>--}}
{{--                    <tr>--}}
{{--                        <th class="bg-gray-50 px-6 py-3 text-center">--}}
{{--                            <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Артикул</span>--}}
{{--                        </th>--}}
{{--                        <th class="bg-gray-50 px-6 py-3 text-center">--}}
{{--                            <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Назва</span>--}}
{{--                        </th>--}}
{{--                        <th class="bg-gray-50 px-6 py-3 text-center">--}}
{{--                            <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Кількіть</span>--}}
{{--                        </th>--}}
{{--                        <th class="bg-gray-50 px-6 py-3 text-center">--}}
{{--                            <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Од.виміру</span>--}}
{{--                        </th>--}}
{{--                        <th class="bg-gray-50 px-6 py-3 text-center">--}}
{{--                            <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Номер документу</span>--}}
{{--                        </th>--}}
{{--                        <th class="bg-gray-50 px-6 py-3 text-center">--}}
{{--                            <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Дата документу</span>--}}
{{--                        </th>--}}
{{--                    </tr>--}}
{{--                    </thead>--}}

{{--                    <tbody class="bg-white divide-y divide-gray-200 divide-solid">--}}

{{--                    @foreach($items as $key=>$item)--}}
{{--                        <tr class="bg-white">--}}
{{--                            <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">--}}
{{--                                <strong>{!! $item->article !!}</strong>--}}
{{--                            </td>--}}
{{--                            <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">--}}
{{--                                <strong>{!! $item->name !!}</strong>--}}
{{--                            </td>--}}
{{--                            <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">--}}
{{--                                <strong>{!! $item->quantity !!}</strong>--}}
{{--                            </td>--}}
{{--                            <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">--}}
{{--                                <strong>{!! $item->unit->unit !!}</strong>--}}
{{--                            </td>--}}
{{--                            <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">--}}
{{--                                <strong>{!! $item->document_number !!}</strong>--}}
{{--                            </td>--}}
{{--                            <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">--}}
{{--                                <strong>{{\Carbon\Carbon::parse($item->document_date)->format('d.m.Y H:i:s')}}</strong>--}}
{{--                            </td>--}}
{{--                            <td>--}}
{{--                                <button wire:click="viewLog('{{$item->designation_id}}','{{$item->designation_number}}')" wire:key="{{ $item->designation_id }}"--}}
{{--                                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">--}}
{{--                                    зміни по вузлу--}}
{{--                                </button>--}}

{{--                            </td>--}}
{{--                        </tr>--}}

{{--                    @endforeach--}}
{{--                    </tbody>--}}
{{--                </table>--}}
{{--                <div class="py-4">--}}
{{--                    {{ $items->appends(request()->input())->links() }}--}}
{{--                </div>--}}
{{--            </div>--}}

{{--        </x-slot:body>--}}
{{--    </x-small-modal-window>--}}
</div>
