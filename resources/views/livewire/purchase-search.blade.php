<div>
    <div class="sm:flex gap-4 py-4">

        <input type="text" wire:model="searchTerm" wire:keydown="updateSearch" placeholder="Пошук Куди"/>

        <input type="text" wire:model="searchTermChto" wire:keydown="updateSearch" placeholder="Пошук Що"/>

{{--    <div class="py-4">--}}
{{--        <input type="checkbox" wire:model="exactMatch" id="exactMatchCheckbox" wire:change="updateSearch">--}}
{{--        <label for="exactMatchCheckbox">Точне співпадіння</label>--}}
{{--    </div>--}}
    </div>
    <div>
        @if(session()->has('message'))
            <div>{{ session('message') }}</div>
        @endif

    </div>
    <div class="min-w-full align-middle">
        <table class="min-w-full border divide-y divide-gray-200">
            <thead>
            <tr>
                <th class="bg-gray-50 px-4 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Куди</span>
                </th>
                <th class="bg-gray-50 px-4 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Що</span>
                </th>
                <th class="bg-gray-50 px-4 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Покупна</span>
                </th>
                <th class="bg-gray-50 px-4 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Кількість</span>
                </th>
                <th class="bg-gray-50 px-4 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Код 1С</span>
                </th>
                <th class="w-56 bg-gray-50 px-4 py-3 text-left">
                </th>
            </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200 divide-solid">
            @foreach($items as $item)
                <tr class="bg-white">
                    <td class="px-4 py-3 leading-5 text-gray-900 whitespace-no-wrap">
                        <strong>{{ $item->designation->designation??'' }}</strong>
                    </td>
                    <td class="px-4 py-3 leading-5 text-gray-900 whitespace-no-wrap">
                        <strong>{{ $item->designationEntry->designation??'' }}</strong>
                    </td>
                    <td class="px-4 py-3 leading-5 text-gray-900 whitespace-no-wrap">
                        <strong>{{ $item->purchase??'' }}</strong>
                    </td>
                    <td class="px-4 py-3 leading-5 text-gray-900 whitespace-no-wrap">
                        <strong>{{ $item->quantity??'' }}</strong>
                    </td>
                    <td class="px-4 py-3 leading-5 text-gray-900 whitespace-no-wrap">
                        <strong>{{ $item->code_1c??'' }}</strong>
                    </td>
                    <td class="px-4 py-3 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                        <a href="{{ route($route.'.edit', $item) }}"
                           class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                            Edit
                        </a>
                        <x-danger-button
                            wire:key="{{ $item->id }}"
                            wire:click="deletePurchase({{ $item->id }})"
                            wire:confirm="Ви впевнені, що хочете видалити запис?">
                            Delete
                        </x-danger-button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="py-4">
            {{ $items->appends(request()->input())->links() }}
        </div>
    </div>
</div>


