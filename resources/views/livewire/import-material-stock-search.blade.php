<div>
    <div class="flex flex-wrap gap-4 justify-between items-center bg-gray-50 p-4 rounded-lg shadow-sm">
        <div class="flex flex-wrap gap-4 items-center bg-gray-50 p-4 rounded-lg">
            <div class="flex flex-col">
                <button wire:click="viewStock"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                    Вигрузити залишки з 1С
                </button>
            </div>

            <div class="flex flex-col">
                <button wire:click="viewStockIn"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                    Вигрузити приход
                </button>
            </div>

{{--            <div class="flex flex-col">--}}
{{--                <button wire:click="viewCode1c"--}}
{{--                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">--}}
{{--                    Код 1С--}}
{{--                </button>--}}
{{--            </div>--}}

            <div class="flex flex-col">
                <input type="text" wire:model.live="searchTerm" wire:keydown="updateSearch" placeholder="Пошук по артикулу" class="rounded-md border-gray-300"/>
            </div>
            <div>
                @if(session()->has('message'))
                    <div>{{ session('message') }}</div>
                @endif
            </div>
        </div>
    </div>

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

    <x-small-modal-window name="viewStock" title="">
        <x-slot:body>
            <div class="sm:flex sm:justify-center px-6 py-6 text-xl font-semibold">
                Вигрузка залишків з 1С
            </div>
            <div class="px-6 py-4">
                <input
                    type="file"
                    wire:model="file"
                    accept=".xlsx,.xls"
                    class="block w-full text-sm text-gray-700"
                >

                @error('file')
                <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                @enderror
            </div>
            <div class="sm:flex sm:justify-center px-6 py-6">
                <button wire:click="confirmStock"
                        @disabled(!$file)
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm">
                    Вигрузити
                </button>
            </div>
        </x-slot:body>
    </x-small-modal-window>

    <x-small-modal-window name="confirmStock" title="Підтвердження">
        <x-slot:body>

            <div class="text-center text-lg font-semibold mb-6">
                Дійсно вигрузити залишки з 1С?
            </div>

            <div class="flex justify-center gap-4">

                <!-- OK -->
                <x-loading-indicator></x-loading-indicator>
                <button
                    wire:click="unloadingStock"
                    class="px-4 py-2 bg-gray-300 rounded-md">
                    OK
                </button>

                <!-- Cancel -->
                <button
                    x-on:click="$dispatch('close-modal', { name: 'confirmExport' })"
                    class="px-4 py-2 bg-gray-300 rounded-md">
                    Cancel
                </button>

            </div>

        </x-slot:body>
    </x-small-modal-window>

{{--**********************************************--}}
    <x-small-modal-window name="viewStockIn" title="">
        <x-slot:body>
            <div class="sm:flex sm:justify-center px-6 py-6 text-xl font-semibold">
                Вигрузка прихода
            </div>
            <div class="px-6 py-4">
                <input
                    type="file"
                    wire:model="file"
                    accept=".xlsx,.xls"
                    class="block w-full text-sm text-gray-700"
                >

                @error('file')
                <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                @enderror
            </div>
            <div class="sm:flex sm:justify-center px-6 py-6">
                <button wire:click="confirmStockIn"
                        @disabled(!$file)
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm">
                    Вигрузити
                </button>
            </div>
        </x-slot:body>
    </x-small-modal-window>



    <x-small-modal-window name="confirmStockIn" title="Підтвердження">
        <x-slot:body>

            <div class="text-center text-lg font-semibold mb-6">
                Дійсно вигрузити приходи?
            </div>

            <div class="flex justify-center gap-4">

                <!-- OK -->
                <x-loading-indicator></x-loading-indicator>
                <button
                    wire:click="unloadingStockIn"
                    class="px-4 py-2 bg-gray-300 rounded-md">
                    OK
                </button>

                <!-- Cancel -->
                <button
                    x-on:click="$dispatch('close-modal', { name: 'confirmStockIn' })"
                    class="px-4 py-2 bg-gray-300 rounded-md">
                    Cancel
                </button>

            </div>

        </x-slot:body>
    </x-small-modal-window>
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
