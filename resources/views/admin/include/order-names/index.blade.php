<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __($title) }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-hidden overflow-x-auto border-b border-gray-200 bg-white p-6">

                    <a href="{{ route($route.'.create') }}"
                       class="mb-4 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                        Створити
                    </a>

                    <div class="min-w-full align-middle">
                        <table class="min-w-full border divide-y divide-gray-200">
                            <thead>
                            <tr>
                                <th class="bg-gray-50 px-3 py-3 text-center">
                                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Назва замовлення</span>
                                </th>
                                <th class="bg-gray-50 px-3 py-3 text-center">
                                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Комплектів</span>
                                </th>
                                <th class="bg-gray-50 px-3 py-3 text-center">
                                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Є замовленням</span>
                                </th>
                                <th class="w-56 bg-gray-50 px-6 py-3 text-left">
                                </th>
                            </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200 divide-solid">

                            @foreach($items as $item)
                                <tr class="bg-white">
                                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                        <strong>{{ $item->name??'' }}</strong>
                                    </td>
                                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                        <strong>{{ $item->quantity??'' }}</strong>
                                    </td>
                                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                        <strong>
                                            <div class="py-4">
                                                <input type="checkbox" disabled  name="is_order" @if($item->is_order) checked @endif id="exactMatchCheckbox" value="1">
                                            </div>
                                        </strong>
                                    </td>
                                    <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                        <a href="{{ route($route.'.edit', $item) }}"
                                           class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                                            Edit
                                        </a>
                                        <form action="{{ route($route.'.destroy', $item) }}" method="POST" onsubmit="return confirm('Ви впевнені, що хочете видалити запис?')" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button>
                                                Delete
                                            </x-danger-button>
                                        </form>
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
</x-app-layout>
