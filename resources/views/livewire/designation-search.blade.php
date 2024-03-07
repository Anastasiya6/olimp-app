<div>
    <div class="py-4">
        <input type="text" wire:model.live="searchTerm" placeholder="Пошук по назві"/>
    </div>

    <div class="min-w-full align-middle">
        <table class="min-w-full border divide-y divide-gray-200">
            <thead>
            <tr>
                <th class="bg-gray-50 px-6 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Номер</span>
                </th>
                <th class="bg-gray-50 px-6 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Назва</span>
                </th>
                <th class="bg-gray-50 px-6 py-3 text-left">
                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Маршрут</span>
                </th>
                <th class="w-56 bg-gray-50 px-6 py-3 text-left">
                </th>
            </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200 divide-solid">

            @foreach($designations as $designation)
                <tr class="bg-white">
                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                        {{ $designation->designation??'' }}
                    </td>
                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                        {{ $designation->name??'' }}
                    </td>
                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap">
                        {{ $designation->route??'' }}
                    </td>
                    <td class="px-16 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                        <a href="{{ route('designations.edit', $designation) }}"
                           class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                            Редагувати
                        </a>
                        {{--<form action="{{ route('designations.destroy', $designation) }}" method="POST" onsubmit="return confirm('Ви впевнені, що хочете видалити запис?')" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <x-danger-button>
                                Видалити
                            </x-danger-button>
                        </form>--}}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="py-4">
            {{ $designations->appends(request()->input())->links() }}
        </div>
    </div>
</div>


