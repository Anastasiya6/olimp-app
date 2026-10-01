<div class="norm-history-page" x-data="{}" x-on:open-modal.window="if ($event.detail?.name === 'viewLog') $dispatch('open-modal', 'norm-history')">
    <div>
        <div>
            <x-catalog.panel class="mb-4">
                <input class="compact-search" type="text" wire:model.live="searchTerm" wire:keydown="updateSearch" aria-label="Пошук за номером вузла" placeholder="Пошук по номеру"/>
            </x-catalog.panel>
            <div class="overflow-hidden rounded-lg border border-[#a8c8c5] bg-white shadow-sm">
                <div class="overflow-x-auto">

                    <div class="min-w-full align-middle">
                        <table class="catalog-table">
                            <thead>
                            <tr>
                                <th scope="col">
                                    Дата
                                </th>
                                <th scope="col">
                                    Номер вузла
                                </th>
                                <th scope="col" class="history-actions">Дії</th>
                            </tr>
                            </thead>

                            <tbody>

                            @foreach($items as $key=>$item)
                                <tr>
                                    <td class="align-top">
                                        {{\Carbon\Carbon::parse($item->created_at)->format('d.m.Y H:i:s')}}
                                    </td>
                                    <td class="align-top">
                                        {!! $item->designation_number !!}
                                    </td>
                                    
                                    <td>
                                        <button wire:click="viewLog('{{$item->designation_id}}','{{$item->designation_number}}')" wire:key="{{ $item->designation_id }}"
                                                class="catalog-button catalog-button-secondary">
                                            Зміни по вузлу
                                        </button>

                                    </td>
                                </tr>

                            @endforeach
                            </tbody>
                        </table>
                        <div class="border-t border-[#a8c8c5] px-4 py-4">
                            {{ $items->appends(request()->input())->links('livewire.pagination.material-stocks') }}
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-modal name="norm-history" maxWidth="7xl" focusable>
        <div role="dialog" aria-modal="true" aria-labelledby="norm-history-title">
            <div class="flex items-center justify-between gap-3 border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4">
                <h3 id="norm-history-title" class="text-xl font-bold text-[#174a47]">Зміни по вузлу {{ $designation_number }}</h3>
                <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити зміни" class="rounded-md p-2 text-[#245b53] hover:bg-white">✕</button>
            </div>
            <div class="overflow-x-auto p-5">
            <div class="min-w-full align-middle">
                <table class="catalog-table">
                    <thead>
                    <tr>
                        <th scope="col">
                            Дата
                        </th>
                        <th scope="col">
                            Матеріал
                        </th>
                        <th scope="col">
                            Норма
                        </th>
                        <th scope="col">
                            Зміни
                        </th>

                    </tr>
                    </thead>

                    <tbody>
                    @if($selectedLog)
                        @foreach($selectedLog as $log)
                            <tr>
                                <td class="align-top">
                                    {{\Carbon\Carbon::parse($log['created_at'])->format('d.m.Y H:i:s')}}
                                </td>
                                <td class="align-top">
                                    {!! $log['material'] !!}
                                </td>
                                <td class="align-top">
                                    {!! $log['norm'] !!}
                                </td>
                                <td class="align-top">
                                    {!! $log['message'] !!}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            </div>
            <div class="flex justify-end border-t border-[#d4e5e0] px-5 py-4"><x-catalog.button x-on:click="$dispatch('close')">Закрити</x-catalog.button></div>
        </div>
    </x-modal>

</div>
