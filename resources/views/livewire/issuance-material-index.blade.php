<div class="issuance-materials-page" x-data="{ pendingDocument: null, posting: false, postError: '' }" x-on:issuance-document-open.window="$dispatch('open-modal', 'issuance-document')">

    {{-- HEADER --}}
    <x-slot name="header" compact="true">
        <h2 class="text-xl font-bold leading-tight text-[#174a47]">
            Видача матеріалів
        </h2>
    </x-slot>
    <div class="bg-[#f2f7f6] py-3 sm:py-4">
        <div class="mx-auto max-w-7xl px-3 sm:px-6 lg:px-8">

            <div class="space-y-4">
                <x-catalog.panel>
                    <div class="flex flex-wrap items-end gap-3">
                        <div class="w-full sm:w-56">
                            <label for="issuance-plan-designation" class="compact-search-label">Деталь з плану або отримувач</label>
                            <input id="issuance-plan-designation" type="search" wire:model.live.debounce.350ms="filterPlanDesignation"
                                placeholder="Позначення деталі або прізвище" class="compact-search" />
                        </div>
                        <div class="w-full sm:w-48">
                            <label for="issuance-filter-order" class="sr-only">Замовлення</label>
                            <select id="issuance-filter-order" wire:model.live="filterOrder" class="h-9 w-full rounded-md border-slate-300 py-1.5 text-sm focus:border-teal-600 focus:ring-teal-600">
                                <option value="">Усі замовлення</option>
                                @foreach($order_names as $order_name)
                                    <option value="{{ $order_name->id }}">{{ $order_name->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" wire:click="resetFilters" class="catalog-button catalog-button-secondary issuance-toolbar-button">Скинути пошук</button>
                        <button type="button" x-on:click="$dispatch('open-modal', 'issuance-reports')" aria-haspopup="dialog"
                            class="catalog-button catalog-button-secondary catalog-reports-button"><svg aria-hidden="true" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Zm0 0v6h6M8 17v-3m4 3v-6m4 6v-2" /></svg>Звіти</button>
                        <button type="button" wire:click="openDocument" wire:loading.attr="disabled" wire:target="openDocument" aria-haspopup="dialog" class="catalog-button catalog-add-button sm:ml-auto">
                            <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
                            Додати документ
                        </button>
                    </div>
                </x-catalog.panel>
                <x-modal name="issuance-document" maxWidth="7xl">
                    <div role="dialog" aria-modal="true" aria-labelledby="issuance-document-title">
                        <div class="border-b border-[#bfd8d1] bg-[#e3f1ee] px-5 py-4">
                            <h3 id="issuance-document-title" class="text-xl font-bold text-[#174a47]">{{ $editingDocumentId ? 'Редагування документа №'.$editingDocumentId : 'Додати документ видачі матеріалів' }}</h3>
                        </div>
                        @if($documentFormLoaded)
                            <livewire:issuance-material-page :id="$editingDocumentId" :in-modal="true" :key="'issuance-document-'.$documentFormVersion" />
                        @endif
                    </div>
                </x-modal>
                <x-modal name="issuance-reports" maxWidth="2xl" focusable>
                    <div class="p-5 sm:p-6" role="dialog" aria-modal="true" aria-labelledby="issuance-reports-title">
                        <div class="mb-5 flex items-center justify-between gap-3 border-b border-[#d4e5e0] pb-4">
                            <h3 id="issuance-reports-title" class="text-xl font-bold text-[#174a47]">Звіти видачі матеріалів</h3>
                            <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити звіти"
                                class="rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-teal-600">
                                <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <form method="GET" action="{{ route('material.issue.generate') }}" target="_blank" rel="noopener">
                        <div class="grid grid-cols-1 items-end gap-3 sm:grid-cols-2">
                            <div>
                                <label for="issuance-report-designation" class="block text-sm text-gray-700">Деталь з плану</label>
                                <input id="issuance-report-designation" type="text" name="designation_number" value="{{ $designation_number }}"
                                    placeholder="Позначення деталі"
                                    class="mt-1 block w-full rounded-md border-gray-300" />
                            </div>
                            <div>
                                <label for="issuance-report-order" class="block text-sm text-gray-700">Замовлення</label>
                                <select id="issuance-report-order" name="order_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300">
                                    @foreach($order_names as $order_name)
                                        <option value="{{ $order_name->id }}" @selected((string) $selectedOrder === (string) $order_name->id)>{{ $order_name->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <button type="submit" name="report" value="detail"
                                    class="catalog-button catalog-button-secondary">
                                    Звіт по деталі та замовленню
                                </button>
                                <button type="submit" name="report" value="order"
                                    class="catalog-button catalog-button-secondary">
                                    Звіт по замовленню
                                </button>
                            </div>
                        </form>
                        <div class="mt-5 border-t border-[#d4e5e0] pt-4">
                            <label for="issuance-report-recipient" class="block text-sm text-gray-700">Отримувач матеріалів</label>
                            <form method="GET" action="{{ route('material.issue.recipient.generate') }}" target="_blank" rel="noopener"
                                class="mt-1 flex flex-col gap-3 sm:flex-row sm:items-center">
                                <select id="issuance-report-recipient" name="recipient" required
                                    class="block w-full min-w-0 rounded-md border-gray-300 sm:flex-1">
                                    <option value="">Оберіть отримувача</option>
                                    @foreach($recipients as $recipient)
                                        <option value="{{ $recipient->id }}">{{ $recipient->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="shrink-0 catalog-button catalog-button-secondary">
                                    Звіт по отримувачу
                                </button>
                            </form>
                            <p class="mt-2 text-xs text-gray-500">За весь час, за всіма замовленнями та ручними видачами.</p>
                        </div>
                        <div class="mt-5 flex justify-end border-t border-[#d4e5e0] pt-4">
                            <button type="button" x-on:click="$dispatch('close')"
                                class="catalog-button catalog-button-secondary">
                                Закрити
                            </button>
                        </div>
                    </div>
                </x-modal>
                <x-modal name="confirm-issuance-post" maxWidth="md" focusable>
                    <div class="p-6" role="dialog" aria-modal="true" aria-labelledby="confirm-issuance-title" aria-describedby="confirm-issuance-description">
                        <h3 id="confirm-issuance-title" class="text-xl font-bold text-[#174a47]">
                            Провести документ № <span x-text="pendingDocument"></span>?
                        </h3>
                        <p id="confirm-issuance-description" class="mt-3 text-sm leading-relaxed text-gray-600">
                            Матеріали цього документа будуть списані зі складу, а документ отримає статус «Проведено».
                        </p>
                        <p x-show="postError" x-text="postError" role="alert" class="mt-3 text-sm text-red-600"></p>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" x-on:click="$dispatch('close')" :disabled="posting"
                                class="catalog-button catalog-button-secondary disabled:opacity-50">
                                Скасувати
                            </button>
                            <button type="button" :disabled="posting || !pendingDocument"
                                x-on:click="
                                    if (posting || !pendingDocument) return;
                                    posting = true;
                                    postError = '';
                                    try {
                                        await $wire.postDocument(pendingDocument);
                                        $dispatch('close-modal', 'confirm-issuance-post');
                                    } catch (error) {
                                        postError = 'Не вдалося провести документ. Оновіть сторінку та перевірте його статус.';
                                    } finally {
                                        posting = false;
                                    }
                                "
                                class="catalog-button catalog-button-primary disabled:cursor-wait">
                                <span x-text="posting ? 'Проведення…' : 'Провести документ'"></span>
                            </button>
                        </div>
                    </div>
                </x-modal>
                <x-modal name="confirm-issuance-unpost" maxWidth="md" focusable>
                    <div class="p-6" role="dialog" aria-modal="true" aria-labelledby="confirm-issuance-unpost-title" aria-describedby="confirm-issuance-unpost-description">
                        <h3 id="confirm-issuance-unpost-title" class="text-xl font-bold text-[#174a47]">
                            Скасувати проведення документа № <span x-text="pendingDocument"></span>?
                        </h3>
                        <p id="confirm-issuance-unpost-description" class="mt-3 text-sm leading-relaxed text-gray-600">
                            Матеріали цього документа будуть повернуті на склад, а документ повернеться до статусу «Чернетка».
                        </p>
                        <p x-show="postError" x-text="postError" role="alert" class="mt-3 text-sm text-red-600"></p>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" x-on:click="$dispatch('close')" :disabled="posting"
                                class="catalog-button catalog-button-secondary disabled:opacity-50">
                                Залишити проведеним
                            </button>
                            <button type="button" :disabled="posting || !pendingDocument"
                                x-on:click="
                                    if (posting || !pendingDocument) return;
                                    posting = true;
                                    postError = '';
                                    try {
                                        await $wire.unpostDocument(pendingDocument);
                                        $dispatch('close-modal', 'confirm-issuance-unpost');
                                    } catch (error) {
                                        postError = 'Не вдалося скасувати проведення. Оновіть сторінку та перевірте його статус.';
                                    } finally {
                                        posting = false;
                                    }
                                "
                                class="catalog-button catalog-button-danger disabled:cursor-wait">
                                <span x-text="posting ? 'Скасування…' : 'Скасувати проведення'"></span>
                            </button>
                        </div>
                    </div>
                </x-modal>
                <a
                    href="{{ route('issuance-materials.bulk-pdf', [
                        'ids' => implode(',', $selectedItems)
                    ]) }}"
                    target="_blank"
                    class="catalog-button catalog-button-secondary"
                >
                    Роздрукувати вибрані
                </a>
                {{-- ТАБЛИЦЯ --}}
                <div class="overflow-hidden rounded-lg border border-[#a8c8c5] bg-white shadow-sm">
                <div class="overflow-x-auto">
                <table class="catalog-table">
                    <thead>
                    <tr>
                        <th scope="col">На друк</th>
                        <th scope="col" class="issuance-id-column">ID</th>
                        <th scope="col" class="issuance-date-column">Дата</th>
                        <th scope="col">Отримав</th>
                        <th scope="col">Замовлення</th>
                        <th scope="col" class="issuance-detail-column">Деталь з плану</th>
                        <th scope="col" class="issuance-detail-column">Деталь (на що брали)</th>
                        <th scope="col">Деталі</th>
                        <th scope="col">Дії</th>
                        <th scope="col">Проведення</th>
                        <th scope="col">Звіт</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $item->id }}"
                                    wire:model.live="selectedItems"
                                >
                            </td>
                            <td class="issuance-id-column">{{ $item->id }}</td>
                            <td class="issuance-date-column">
                                <span class="block whitespace-nowrap">{{ $item->created_at?->format('d.m.Y') }}</span>
                                <span class="block whitespace-nowrap">{{ $item->created_at?->format('H:i:s') }}</span>
                            </td>
                            <td>{{$item->receivedByUser?->name ?? '-'}}</td>
                            <td>{{$item->order_name->name}}</td>
                            <td class="issuance-detail-column">{{$item->planTaskDesignation?->designation}}</td>
                            <td class="issuance-detail-column">{{$item->designation->designation}}</td>
                            <td class="text-sm">
                                {{ collect($item->items ?? [])->pluck('details')->filter()->implode(', ') }}
                            </td>
                            <td>
                                {{-- РЕДАГУВАННЯ --}}
                                <button type="button" wire:click="openDocument({{ $item->id }})" wire:loading.attr="disabled" wire:target="openDocument"
                                    aria-haspopup="dialog" class="catalog-button catalog-button-secondary">
                                    Редагувати
                                </button>
                            </td>
                            <td>
                                @if($item->status === 'draft')
                                    <button
                                        type="button"
                                        :disabled="posting"
                                        x-on:click="pendingDocument = {{ $item->id }}; postError = ''; $dispatch('open-modal', 'confirm-issuance-post')"
                                        class="catalog-button catalog-button-secondary"
                                    >
                                        Провести
                                    </button>
                                @else
                                    <button
                                        type="button"
                                        :disabled="posting"
                                        x-on:click="pendingDocument = {{ $item->id }}; postError = ''; $dispatch('open-modal', 'confirm-issuance-unpost')"
                                        class="catalog-button catalog-button-danger"
                                    >
                                        Відмінити
                                    </button>
                                @endif
                            </td>
                            <td>
                                <a
                                    href="{{ route('issuance-materials.pdf', $item->id) }}"
                                    target="_blank"
                                    class="catalog-button catalog-button-secondary"
                                >
                                    PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="p-4 text-center">
                                @if($filterOrder !== '' || trim($filterPlanDesignation) !== '')
                                    За вказаними умовами документів не знайдено.
                                @else
                                    Немає документів
                                @endif
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                </div>

                {{-- PAGINATION --}}
                <div class="border-t border-[#a8c8c5] bg-white px-4 py-4">
                    <p class="mb-3 text-sm text-slate-600" role="status">Знайдено документів: {{ $items->total() }}</p>
                    {{ $items->links() }}
                </div>
                </div>

            </div>

        </div>
    </div>
</div>
