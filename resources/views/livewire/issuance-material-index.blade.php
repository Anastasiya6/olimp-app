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
                        <th class="p-2 border">Дія</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td class="p-2 border">{{ $item->id }}</td>
                            <td class="p-2 border">{{ $item->created_at }}</td>
                            <td class="p-2 border">
                                <a
                                    wire:navigate
                                    href="#"
                                    class="text-blue-600"
                                >
                                    Відкрити
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
