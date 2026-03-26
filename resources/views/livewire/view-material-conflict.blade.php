
    <x-small-modal-window name="viewMaterialConflict" title="">
        <x-slot:body>
            <div class="min-w-full align-middle">
                <table class="min-w-full border divide-y divide-gray-200">
                    <thead>
                    <tr>
                        <th class="bg-gray-50 px-6 py-3 text-center">
                            <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Артикул</span>
                        </th>
                        <th class="bg-gray-50 px-6 py-3 text-center">
                            <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Назва</span>
                        </th>
                        <th class="bg-gray-50 px-6 py-3 text-center">
                            <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Кількіть</span>
                        </th>
                        <th class="bg-gray-50 px-6 py-3 text-center">
                            <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Од.виміру</span>
                        </th>
                        <th class="bg-gray-50 px-6 py-3 text-center">
                            <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Номер документу</span>
                        </th>
                        <th class="bg-gray-50 px-6 py-3 text-center">
                            <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Дата документу</span>
                        </th>
                    </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200 divide-solid">

                    @foreach($items as $key=>$item)
                        <tr class="bg-white">
                            <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                <strong>{!! $item->article !!}</strong>
                            </td>
                            <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                <strong>{!! $item->name !!}</strong>
                            </td>
                            <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                <strong>{!! $item->quantity !!}</strong>
                            </td>
                            <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                <strong>{!! $item->unit->unit !!}</strong>
                            </td>
                            <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                <strong>{!! $item->document_number !!}</strong>
                            </td>
                            <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                <strong>{{\Carbon\Carbon::parse($item->document_date)->format('d.m.Y H:i:s')}}</strong>
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
        </x-slot:body>
    </x-small-modal-window>

