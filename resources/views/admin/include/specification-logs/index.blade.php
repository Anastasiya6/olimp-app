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

                    <div class="min-w-full align-middle">
                        <table class="min-w-full border divide-y divide-gray-200">
                            <thead>
                            <tr>
                                <th class="bg-gray-50 px-6 py-3 text-center">
                                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Номер вузла</span>
                                </th>
                                <th class="bg-gray-50 px-6 py-3 text-center">
                                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Назва вузла</span>
                                </th>
                                <th class="bg-gray-50 px-6 py-3 text-center">
                                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Номер деталі</span>
                                </th>
                                <th class="bg-gray-50 px-6 py-3 text-center">
                                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Назва деталі</span>
                                </th>
                                <th class="bg-gray-50 px-6 py-3 text-center">
                                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Зміни</span>
                                </th>
                                <th class="bg-gray-50 px-6 py-3 text-center">
                                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Дата</span>
                                </th>
                            </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200 divide-solid">

                            @foreach($items as $item)
                                <tr class="bg-white">
                                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                        <strong>{!! $item->designation_number !!}</strong>
                                    </td>
                                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                        <strong>{{ $item->designation }}</strong>
                                    </td>
                                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                        <strong>{{ $item->detail_number }}</strong>
                                    </td>
                                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                        <strong>{{ $item->detail }}</strong>
                                    </td>
                                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                        <strong>{{ $item->message }}</strong>
                                    </td>
                                    <td class="px-6 py-4 leading-5 text-gray-900 whitespace-no-wrap text-center">
                                        <strong>{{\Carbon\Carbon::parse($item->created_at)->format('d.m.Y H:i:s')}}</strong>
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
