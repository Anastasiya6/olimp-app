<x-welcome-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('М0020') }}
        </h2>
    </x-slot>

    <h2>Відомість застосування</h2>
    <div class="printable-content">
        <table>
            <thead>
            <tr>
                <th>заказ</th>
                <th>обозн. детали<br>сбор.ед (что)</th>
                <th>Наименование <br>дсе</th>
                <th>обозн.сбор.ед<br>(куда)</th>
                <th>кол. на <br>узел</th>
                <th>кол. на <br>изделие</th>
                <th>техмаршрут</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $row)
                <tr>
                    <td>{{ $row->zakaz }}</td>
                    <td>{{ $row->chto }}</td>
                    <td>{{ $row->chto_name }}</td>
                    <td>{{ $row->kuda }}</td>
                    <td>{{ $row->kols }}</td>
                    <td>{{ $row->kolzak }}</td>
                    <td>{{ $row->hcp }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-welcome-layout>
