<div>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="py-4">
                <input type="text" wire:model.live="searchTerm" wire:keydown="updateSearch" placeholder="Пошук по номеру"/>
            </div>
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-hidden overflow-x-auto border-b border-gray-200 bg-white p-6">

                    <div class="min-w-full align-middle">
                        <table class="min-w-full border divide-y divide-gray-200">
                            <thead>
                            <tr>
                                <th class="bg-gray-50 px-6 py-3 text-center">
                                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Дата</span>
                                </th>
                                <th class="bg-gray-50 px-6 py-3 text-center">
                                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Номер вузла</span>
                                </th>
                            </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200 divide-solid">

                            @foreach($items as $key=>$item)
                                <tr class="bg-white">
                                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                        <strong>{{\Carbon\Carbon::parse($item->created_at)->format('d.m.Y H:i:s')}}</strong>
                                    </td>
                                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                        <strong>{!! $item->designation_number !!}</strong>
                                    </td>
                                    <td>
                                        <button wire:click="viewLog('{{$item->designation_id}}','{{$item->designation_number}}')" wire:key="{{ $item->designation_id }}"
                                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                                            зміни по вузлу
                                        </button>

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
            </div>
        </div>
    </div>
    @if($selectedLog)
        <x-modal-window name="viewLog" title="{{$designation_number}}">
            <x-slot:body>
                <div class="min-w-full align-middle">
                    <table class="min-w-full border divide-y divide-gray-200">
                        <thead>
                        <tr>
                            <th class="bg-gray-50 px-6 py-3 text-center">
                                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Дата</span>
                            </th>
                            <th class="bg-gray-50 px-6 py-3 text-center">
                                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Матеріал</span>
                            </th>
                            <th class="bg-gray-50 px-6 py-3 text-center">
                                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Норма</span>
                            </th>
                            <th class="bg-gray-50 px-6 py-3 text-center">
                                <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Зміни</span>
                            </th>

                        </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200 divide-solid">

                        @foreach($selectedLog as $log)
                            <tr class="bg-white">
                                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                    <strong>{{\Carbon\Carbon::parse($log['created_at'])->format('d.m.Y H:i:s')}}</strong>
                                </td>
                                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                    <strong>{!! $log['material'] !!}</strong>
                                </td>
                                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                    <strong>{!! $log['norm'] !!}</strong>
                                </td>
                                <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                    <strong>{!! $log['message'] !!}</strong>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </x-slot:body>
        </x-modal-window>
    @endif
</div>
