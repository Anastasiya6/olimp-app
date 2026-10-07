<div class="issuance-materials-page" x-data="{ pendingDocument: null, posting: false, postError: '', deleting: false, deleteError: '' }">

    <x-slot name="header" compact="true">
        <h2 class="text-xl font-bold leading-tight text-[#174a47]">
            Видача матеріалів без норм
        </h2>
    </x-slot>
    <div class="bg-[#f2f7f6] py-3 sm:py-4">
        <div class="mx-auto max-w-7xl px-3 sm:px-6 lg:px-8">

                <x-catalog.panel class="mb-4">
                    <div class="flex flex-wrap justify-end gap-3">
                        <x-catalog.button class="catalog-reports-button" x-on:click="$dispatch('open-modal', 'manual-issuance-reports')" aria-haspopup="dialog">
                            <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Zm0 0v6h6M8 17v-3m4 3v-6m4 6v-2" /></svg>
                            Звіти
                        </x-catalog.button>
                        <x-catalog.button variant="primary" class="catalog-add-button" x-on:click="$dispatch('open-modal', 'create-manual-issuance')" aria-haspopup="dialog">
                            <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
                            Додати документ
                        </x-catalog.button>
                    </div>

                </x-catalog.panel>

                <x-modal name="manual-issuance-reports" maxWidth="2xl" focusable>
                    <div role="dialog" aria-modal="true" aria-labelledby="manual-issuance-reports-title">
                        <div class="flex items-center justify-between gap-3 border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4">
                            <h3 id="manual-issuance-reports-title" class="text-xl font-bold text-[#174a47]">Звіт по замовленню</h3>
                            <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити" class="rounded-md p-2 text-[#245b53] hover:bg-white">
                                <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div class="space-y-4 p-5">
                            <label class="flex items-center gap-2 rounded-md border border-[#bfd8d1] bg-[#f2f7f6] px-3 py-2 text-sm font-medium text-[#245b53]">
                                <input type="checkbox" wire:model.live="postedOnly" class="rounded border-gray-300 text-teal-700 focus:ring-teal-600">
                                Лише проведені документи
                            </label>
                            <label class="block">
                                <span class="font-medium text-[#245b53]">Замовлення</span>
                                <select wire:model.live="reportOrderId" class="mt-1 block w-full rounded-md border-slate-300 focus:border-teal-600 focus:ring-teal-600">
                                    <option value="">Оберіть замовлення</option>
                                    @foreach($order_names as $order)
                                        <option value="{{ $order->id }}">{{ $order->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <label class="block">
                                    <span class="font-medium text-[#245b53]">З</span>
                                    <input type="date" wire:model.live="reportDateFrom" class="mt-1 block w-full rounded-md border-slate-300 focus:border-teal-600 focus:ring-teal-600">
                                </label>
                                <label class="block">
                                    <span class="font-medium text-[#245b53]">По</span>
                                    <input type="date" wire:model.live="reportDateTo" min="{{ $reportDateFrom }}" class="mt-1 block w-full rounded-md border-slate-300 focus:border-teal-600 focus:ring-teal-600">
                                </label>
                            </div>
                            <div class="flex justify-end">
                                @if($reportOrderId)
                                    <a href="{{ route('material.issue.order.pdf', ['order' => $reportOrderId, 'posted_only' => $postedOnly ? 1 : 0, 'date_from' => $reportDateFrom, 'date_to' => $reportDateTo]) }}" target="_blank" class="catalog-button catalog-reports-button">Звіт по замовленню</a>
                                @else
                                    <button type="button" disabled class="catalog-button catalog-reports-button cursor-not-allowed opacity-50">Звіт по замовленню</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </x-modal>

                <x-modal name="create-manual-issuance" maxWidth="2xl" focusable>
                    <div role="dialog" aria-modal="true" aria-labelledby="create-manual-issuance-title">
                        <div class="flex items-center justify-between gap-3 border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4">
                            <h3 id="create-manual-issuance-title" class="text-xl font-bold text-[#174a47]">Видача матеріалів без норм</h3>
                            <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити" class="rounded-md p-2 text-[#245b53] hover:bg-white">
                                <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <livewire:manual-issuance-material-page :in-modal="true" />
                    </div>
                </x-modal>
                <x-modal name="confirm-manual-issuance-post" maxWidth="md" focusable>
                    <div class="p-6" role="dialog" aria-modal="true" aria-labelledby="manual-issuance-post-title">
                        <h3 id="manual-issuance-post-title" class="text-xl font-bold text-[#174a47]">Провести документ № <span x-text="pendingDocument"></span>?</h3>
                        <p class="mt-3 text-sm leading-relaxed text-gray-600">Матеріали документа будуть списані зі складу, а документ отримає статус «Проведено».</p>
                        <p x-show="postError" x-text="postError" role="alert" class="mt-3 text-sm text-red-600"></p>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" x-on:click="$dispatch('close')" :disabled="posting" class="catalog-button catalog-button-secondary disabled:opacity-50">Скасувати</button>
                            <button type="button" :disabled="posting || !pendingDocument" x-on:click="posting = true; postError = ''; try { await $wire.postDocument(pendingDocument); $dispatch('close-modal', 'confirm-manual-issuance-post'); } catch (error) { postError = 'Не вдалося провести документ. Оновіть сторінку та перевірте його статус.'; } finally { posting = false; }" class="catalog-button catalog-button-primary disabled:cursor-wait"><span x-text="posting ? 'Проведення…' : 'Провести документ'"></span></button>
                        </div>
                    </div>
                </x-modal>
                <x-modal name="confirm-manual-issuance-unpost" maxWidth="md" focusable>
                    <div class="p-6" role="dialog" aria-modal="true" aria-labelledby="manual-issuance-unpost-title">
                        <h3 id="manual-issuance-unpost-title" class="text-xl font-bold text-[#174a47]">Скасувати проведення документа № <span x-text="pendingDocument"></span>?</h3>
                        <p class="mt-3 text-sm leading-relaxed text-gray-600">Матеріали документа будуть повернуті на склад, а документ повернеться до статусу «Чернетка».</p>
                        <p x-show="postError" x-text="postError" role="alert" class="mt-3 text-sm text-red-600"></p>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" x-on:click="$dispatch('close')" :disabled="posting" class="catalog-button catalog-button-secondary disabled:opacity-50">Залишити проведеним</button>
                            <button type="button" :disabled="posting || !pendingDocument" x-on:click="posting = true; postError = ''; try { await $wire.unpostDocument(pendingDocument); $dispatch('close-modal', 'confirm-manual-issuance-unpost'); } catch (error) { postError = 'Не вдалося скасувати проведення. Оновіть сторінку та перевірте його статус.'; } finally { posting = false; }" class="catalog-button catalog-button-danger disabled:cursor-wait"><span x-text="posting ? 'Скасування…' : 'Скасувати проведення'"></span></button>
                        </div>
                    </div>
                </x-modal>
                <x-modal name="confirm-manual-issuance-delete" maxWidth="md" focusable>
                    <div class="p-6" role="dialog" aria-modal="true" aria-labelledby="manual-issuance-delete-title">
                        <h3 id="manual-issuance-delete-title" class="text-xl font-bold text-[#174a47]">Видалити документ № <span x-text="pendingDocument"></span>?</h3>
                        <p class="mt-3 text-sm leading-relaxed text-gray-600">Документ буде видалено. Якщо документ проведений, матеріали повернуться на склад.</p>
                        <p x-show="deleteError" x-text="deleteError" role="alert" class="mt-3 text-sm text-red-600"></p>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" x-on:click="$dispatch('close')" :disabled="deleting" class="catalog-button catalog-button-secondary disabled:opacity-50">Скасувати</button>
                            <button type="button" :disabled="deleting || !pendingDocument" x-on:click="deleting = true; deleteError = ''; try { await $wire.deleteDocument(pendingDocument); $dispatch('close-modal', 'confirm-manual-issuance-delete'); } catch (error) { deleteError = 'Не вдалося видалити документ. Оновіть сторінку та перевірте його стан.'; } finally { deleting = false; }" class="catalog-button catalog-button-danger disabled:cursor-wait"><span x-text="deleting ? 'Видалення…' : 'Видалити документ'"></span></button>
                        </div>
                    </div>
                </x-modal>
                <div class="overflow-hidden rounded-lg border border-[#a8c8c5] bg-white shadow-sm">
                <div class="overflow-x-auto">
                <table class="catalog-table">
                    <thead>
                    <tr>
                        <th scope="col" class="issuance-id-column">ID</th>
                        <th scope="col" class="issuance-date-column">Дата</th>
                        <th scope="col">Замовлення</th>
                        <th scope="col">Отримав</th>
                        <th scope="col">Матеріал</th>
                        <th scope="col">Кількість</th>
                        <th scope="col">Проведення</th>
                        <th scope="col">Видалити</th>

                        <th scope="col">Звіт</th>
                    </tr>
                    </thead>
                    <tbody>

                    @forelse($items as $item)
                        <tr>
                            <td class="issuance-id-column">{{ $item->id }}</td>
                            <td class="issuance-date-column">
                                <span class="block whitespace-nowrap">{{ $item->created_at?->format('d.m.Y') }}</span>
                                <span class="block whitespace-nowrap">{{ $item->created_at?->format('H:i:s') }}</span>
                            </td>
                            <td>{{ $item->order_name?->name ?? '—' }}</td>
                            <td>{{$item->receivedByUser->name}}</td>
                            <td>
                                @foreach($item->items as $issuanceItem)
                                    <div>{{ $issuanceItem->importMaterial?->name }}</div>
                                @endforeach
                            </td>
                            <td>
                                @foreach($item->items as $issuanceItem)
                                    <div>{{ $issuanceItem->quantity }}</div>
                                @endforeach
                            </td>
                            <td>
                                @if($item->status === 'posted')
                                    <button type="button" :disabled="posting" x-on:click="pendingDocument = {{ $item->id }}; postError = ''; $dispatch('open-modal', 'confirm-manual-issuance-unpost')" class="catalog-button catalog-button-danger">Скасувати</button>
                                @else
                                    <button type="button" :disabled="posting" x-on:click="pendingDocument = {{ $item->id }}; postError = ''; $dispatch('open-modal', 'confirm-manual-issuance-post')" class="catalog-button catalog-button-secondary">Провести</button>
                                @endif
                            </td>

                            <td>
                                <button type="button" :disabled="deleting" x-on:click="pendingDocument = {{ $item->id }}; deleteError = ''; $dispatch('open-modal', 'confirm-manual-issuance-delete')" class="catalog-button catalog-button-danger">Видалити</button>
                            </td>

                            <td>
                                <a
                                    href="{{ route('manual-issuance-materials.pdf', $item->id) }}"
                                    target="_blank"
                                    class="catalog-button catalog-button-secondary"
                                >
                                    PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-4 text-center">
                                Немає документів
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                </div>

                <div class="border-t border-[#a8c8c5] bg-white px-4 py-4">
                    <p class="mb-3 text-sm text-slate-600">Записів: {{ $items->total() }}</p>
                    {{ $items->links() }}
                </div>

            </div>

        </div>
    </div>
</div>
