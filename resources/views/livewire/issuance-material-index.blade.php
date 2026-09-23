<div x-data="{ pendingDocument: null, posting: false, postError: '' }">

    {{-- HEADER --}}
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Видача матеріалів
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                {{-- КНОПКА --}}
                <div class="mb-4">
                    <a
                        href="{{ route('issuance-materials.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                    >
                        Додати документ
                    </a>
                </div>
                <div class="my-4 rounded-md border border-gray-200 bg-gray-50">
                    <div class="min-w-0 p-4 lg:p-5">
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <h3 class="font-semibold text-gray-800">Пошук документів видачі</h3>
                            <button type="button" x-on:click="$dispatch('open-modal', 'issuance-reports')"
                                aria-haspopup="dialog"
                                class="shrink-0 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Звіти
                            </button>
                        </div>
                        <div class="grid grid-cols-1 items-end gap-3 sm:grid-cols-2">
                            <div>
                                <label for="issuance-plan-designation" class="block text-sm text-gray-700">Деталь з плану або отримувач</label>
                                <input id="issuance-plan-designation" type="search"
                                    wire:model.live.debounce.350ms="filterPlanDesignation"
                                    placeholder="Позначення деталі або прізвище"
                                    class="mt-1 block w-full rounded-md border-gray-300" />
                            </div>
                            <div>
                                <label for="issuance-filter-order" class="block text-sm text-gray-700">Замовлення</label>
                                <select id="issuance-filter-order" wire:model.live="filterOrder"
                                    class="mt-1 block w-full rounded-md border-gray-300">
                                    <option value="">Усі замовлення</option>
                                    @foreach($order_names as $order_name)
                                        <option value="{{ $order_name->id }}">{{ $order_name->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                            <button type="button" wire:click="resetFilters"
                                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Скинути пошук
                            </button>
                            <p class="text-sm text-gray-600" role="status">Знайдено документів: {{ $items->total() }}</p>
                        </div>
                    </div>
                </div>
                <x-modal name="issuance-reports" maxWidth="2xl" focusable>
                    <div class="p-5 sm:p-6" role="dialog" aria-modal="true" aria-labelledby="issuance-reports-title">
                        <div class="mb-5 flex items-center justify-between gap-3 border-b border-gray-200 pb-4">
                            <h3 id="issuance-reports-title" class="text-lg font-semibold text-gray-800">Звіти видачі матеріалів</h3>
                            <button type="button" x-on:click="$dispatch('close')" aria-label="Закрити звіти"
                                class="rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
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
                                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Звіт по деталі та замовленню
                                </button>
                                <button type="submit" name="report" value="order"
                                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Звіт по замовленню
                                </button>
                            </div>
                        </form>
                        <div class="mt-5 border-t border-gray-200 pt-4">
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
                                <button type="submit" class="shrink-0 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Звіт по отримувачу
                                </button>
                            </form>
                            <p class="mt-2 text-xs text-gray-500">За весь час, за всіма замовленнями та ручними видачами.</p>
                        </div>
                        <div class="mt-5 flex justify-end border-t border-gray-200 pt-4">
                            <button type="button" x-on:click="$dispatch('close')"
                                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Закрити
                            </button>
                        </div>
                    </div>
                </x-modal>
                <x-modal name="confirm-issuance-post" maxWidth="md" focusable>
                    <div class="p-6" role="dialog" aria-modal="true" aria-labelledby="confirm-issuance-title" aria-describedby="confirm-issuance-description">
                        <h3 id="confirm-issuance-title" class="text-lg font-semibold text-gray-800">
                            Провести документ № <span x-text="pendingDocument"></span>?
                        </h3>
                        <p id="confirm-issuance-description" class="mt-3 text-sm leading-relaxed text-gray-600">
                            Матеріали цього документа будуть списані зі складу, а документ отримає статус «Проведено».
                        </p>
                        <p x-show="postError" x-text="postError" role="alert" class="mt-3 text-sm text-red-600"></p>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" x-on:click="$dispatch('close')" :disabled="posting"
                                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 disabled:opacity-50">
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
                                class="rounded-md bg-green-700 px-4 py-2 text-sm font-medium text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:cursor-wait disabled:opacity-50">
                                <span x-text="posting ? 'Проведення…' : 'Провести документ'"></span>
                            </button>
                        </div>
                    </div>
                </x-modal>
                <x-modal name="confirm-issuance-unpost" maxWidth="md" focusable>
                    <div class="p-6" role="dialog" aria-modal="true" aria-labelledby="confirm-issuance-unpost-title" aria-describedby="confirm-issuance-unpost-description">
                        <h3 id="confirm-issuance-unpost-title" class="text-lg font-semibold text-gray-800">
                            Скасувати проведення документа № <span x-text="pendingDocument"></span>?
                        </h3>
                        <p id="confirm-issuance-unpost-description" class="mt-3 text-sm leading-relaxed text-gray-600">
                            Матеріали цього документа будуть повернуті на склад, а документ повернеться до статусу «Чернетка».
                        </p>
                        <p x-show="postError" x-text="postError" role="alert" class="mt-3 text-sm text-red-600"></p>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" x-on:click="$dispatch('close')" :disabled="posting"
                                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 disabled:opacity-50">
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
                                class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:cursor-wait disabled:opacity-50">
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
                    class="text-blue-600 hover:underline"
                >
                    Роздрукувати вибрані
                </a>
                {{-- ТАБЛИЦЯ --}}
                <table class="w-full border">
                    <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border">На друк</th>
                        <th class="p-2 border">ID</th>
                        <th class="p-2 border">Дата</th>
                        <th class="p-2 border">Отримав</th>
                        <th class="p-2 border">Замовлення</th>
                        <th class="p-2 border">Деталь з плану</th>
                        <th class="p-2 border">Деталь (на що брали)</th>
                        <th class="p-2 border">Деталі</th>
                        <th class="p-2 border">Дія</th>
                        <th class="p-2 border">Дія</th>
                        <th class="p-2 border">Звіт</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td class="p-2 border">
                                <input
                                    type="checkbox"
                                    value="{{ $item->id }}"
                                    wire:model.live="selectedItems"
                                >
                            </td>
                            <td class="p-2 border">{{ $item->id }}</td>
                            <td class="p-2 border">{{ $item->created_at }}</td>
                            <td class="p-2 border">{{$item->receivedByUser?->name ?? '-'}}</td>
                            <td class="p-2 border">{{$item->order_name->name}}</td>
                            <td class="p-2 border">{{$item->planTaskDesignation?->designation}}</td>
                            <td class="p-2 border">{{$item->designation->designation}}</td>
                            <td class="p-2 border text-xs">
                                {{ collect($item->items ?? [])->pluck('details')->filter()->implode(', ') }}
                            </td>
                            <td class="p-2 border">
                                {{-- РЕДАГУВАННЯ --}}
                                <a
                                    href="{{ route('issuance-materials.edit', $item->id) }}"
                                    class="text-blue-600 hover:underline"
                                >
                                    Редагувати
                                </a>
                            </td>
                            <td>
                                @if($item->status === 'draft')
                                    <button
                                        type="button"
                                        :disabled="posting"
                                        x-on:click="pendingDocument = {{ $item->id }}; postError = ''; $dispatch('open-modal', 'confirm-issuance-post')"
                                        class="text-green-600 hover:underline ml-2"
                                    >
                                        Провести
                                    </button>
                                @else
                                    <button
                                        type="button"
                                        :disabled="posting"
                                        x-on:click="pendingDocument = {{ $item->id }}; postError = ''; $dispatch('open-modal', 'confirm-issuance-unpost')"
                                        class="text-red-600 hover:underline ml-2"
                                    >
                                        Відмінити
                                    </button>
                                @endif
                            </td>
                            <td class="p-2 border">
                                <a
                                    href="{{ route('issuance-materials.pdf', $item->id) }}"
                                    target="_blank"
                                    class="text-blue-600 hover:underline"
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

                {{-- PAGINATION --}}
                <div class="mt-4">
                    {{ $items->links() }}
                </div>

            </div>

        </div>
    </div>
</div>
