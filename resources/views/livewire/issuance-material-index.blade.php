<div>

    {{-- HEADER --}}
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Видача матеріалів
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                {{-- КНОПКА --}}
                <div class="mb-4">
                    <x-primary-button
                        wire:navigate
                        href="{{ route('issuance-materials.create') }}"
                    >
                        Додати документ
                    </x-primary-button>
                </div>

                {{-- ТАБЛИЦЯ --}}
                <table class="w-full border">
                    <thead>
                    <tr class="bg-gray-100">

                        <th class="p-2 border">ID</th>
                        <th class="p-2 border">Дата</th>
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
                            <td class="p-2 border">{{ $item->id }}</td>
                            <td class="p-2 border">{{ $item->created_at }}</td>
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
                                        wire:click="postDocument({{ $item->id }})"
                                        wire:confirm="Провести документ?"
                                        class="text-green-600 hover:underline ml-2"
                                    >
                                        Провести
                                    </button>
                                @else
                                    <button
                                        wire:click="unpostDocument({{ $item->id }})"
                                        wire:confirm="Відмінити проведення?"
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
                            <td colspan="3" class="p-4 text-center">
                                Немає документів
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
