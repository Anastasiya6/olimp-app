<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Звіт</title>

    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #f3f3f3; }
    </style>
</head>
<body>
<h2 style="text-align:center; margin-bottom:20px;">
    Звіт по вузлу {{ $designation }}
    Замовлення {{$order}}
</h2>
<table>
    <thead>
    <tr>
        <th>Деталь</th>
        <th>Матеріал</th>
        <th>Норма витрат на виріб</th>
        <th>Од.</th>
        <th>Документи</th>
        <th>Комплект</th>
        <th>Кількість</th>
    </tr>
    </thead>

    <tbody>
    @foreach($all_materials as $key=>$item)
        <tr>
            <td>{{ $item['detail'] }}</td>
            <td>{{ $item['material'] }}</td>
            <td>{{ $item['norm'] }}</td>
            <td>{{ $item['unit'] }}</td>
            @if(is_numeric($key))
                @if(!empty($result['material_id'][$key]))
                    <td>
                        @foreach ($result['material_id'][$key] as $record)
                            {{ $record['item']->material_issuance_id  }}@if(!$loop->last), @endif
                        @endforeach
                    </td>
                    <td>
                        {{ collect($result['material_id'][$key])->sum('quantity') }}
                    </td>
                    <td>
                        {{ collect($result['material_id'][$key])->sum('item.quantity') }}
                    </td>
                @else
                    <td></td>
                    <td>0</td>
                @endif
            @else
                @php
                    $designationKey = (int)$key;
                @endphp

                @if(!empty($result['designation_id'][$designationKey]))
                    <td>
                        @foreach ($result['designation_id'][$designationKey] as $record)
                            {{ $record['item']->material_issuance_id }}@if(!$loop->last), @endif
                        @endforeach
                    </td>
                    <td>
                        {{ collect($result['designation_id'][$designationKey])->sum('quantity') }}
                    </td>
                    <td>
                        {{ collect($result['designation_id'][$designationKey])->sum('item.quantity') }}
                    </td>
                @else
                    <td></td>
                    <td>0</td>
                @endif
            @endif
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
