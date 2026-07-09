<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Звіт</title>

    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
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
        <th>Матеріал з 1С</th>
        <th>Норма витрат на виріб</th>
        <th>Од.</th>
        <th>Документи</th>
        <th>Кіл-ть деталей</th>
        <th>Разом</th>
        <th>Кіл-ть матер.</th>
        <th>Факт. кіл-ть матер.</th>
        <th>Кіл-ть з плану</th>
    </tr>
    </thead>

    <tbody>
    @foreach($all_materials as $key=>$item)
        @php
            $details = array_map('trim', explode(',', $item['detail']));

            if (is_numeric($key)) {
                $records = $result['material_id'][$key] ?? [];
            } else {
                $records = $result['designation_id'][(int)$key] ?? [];
            }

            $groupRecords = $records[$item['detail']] ?? null;

            if ($groupRecords) {
                $firstRecord = collect($groupRecords)->first();
            } else {
                $firstRecord = null;
                foreach ($details as $detail) {
                    if (!empty($records[$detail])) {
                        $firstRecord = collect($records[$detail])->first();
                        break;
                    }
                }
            }
        @endphp
        <tr>
            <td style="white-space: nowrap;">
                @foreach($details as $detail)
                    {{ $detail }}<br>
                @endforeach
            </td>
            <td>{{ $item['material'] }}</td>
            <td>
                @if($firstRecord)
                    {{ $firstRecord['item']->importMaterial?->article }}
                    {{ $firstRecord['item']->importMaterial?->name }}
                @endif
            </td>
            <td>
                @foreach($details as $detail)
                    {{ $item['norm_list'][trim($detail)] ?? '' }}<br>
                @endforeach
            </td>
            <td>{{ $item['unit'] }}</td>

            <td>
                @if($groupRecords)
                    @foreach($groupRecords as $record)
                        {{ $record['item']->material_issuance_id }}@if(!$loop->last), @endif
                    @endforeach
                @else
                    @foreach($details as $detail)
                        @foreach($records[$detail] ?? [] as $record)
                            {{ $record['item']->material_issuance_id }}@if(!$loop->last), @endif
                        @endforeach
                        <br>
                    @endforeach
                @endif
            </td>
            <td>
                @if($groupRecords)
                    @foreach($groupRecords as $record)
                        {{ $record['quantity'] }}@if(!$loop->last), @endif
                    @endforeach
                @else
                    @foreach($details as $detail)
                        @foreach($records[$detail] ?? [] as $record)
                            {{ $record['quantity'] }}@if(!$loop->last), @endif
                        @endforeach
                        <br>
                    @endforeach
                @endif
            </td>
            <td>
                @if($groupRecords)
                    {{ collect($groupRecords)->sum(fn($record) => $record['quantity']) }}
                @else
                    @foreach($details as $detail)
                        {{ collect($records[$detail] ?? [])->sum(fn($record) => $record['quantity']) }}
                        <br>
                    @endforeach
                @endif
            </td>
            <td>
                @if($groupRecords)
                    @foreach($groupRecords as $record)
                        {{ $record['item']->quantity }}@if(!$loop->last), @endif
                    @endforeach
                @else
                    @foreach($details as $detail)
                        @foreach($records[$detail] ?? [] as $record)
                            {{ $record['item']->quantity }}@if(!$loop->last), @endif
                        @endforeach
                        <br>
                    @endforeach
                @endif
            </td>
            <td>
                @if($groupRecords)
                    @foreach($groupRecords as $record)
                        {{ $record['item']->fact_quantity }}@if(!$loop->last), @endif
                    @endforeach
                @else
                    @foreach($details as $detail)
                        @foreach($records[$detail] ?? [] as $record)
                            {{ $record['item']->fact_quantity }}@if(!$loop->last), @endif
                        @endforeach
                        <br>
                    @endforeach
                @endif
            </td>
            <td style="white-space: nowrap;">
                @foreach($details as $detail)
                    @if(isset($item['specification_quantity'][trim($detail)]))
                        {{ $item['specification_quantity'][trim($detail)] . ' * ' . $order_quantity . ' = ' . ($item['specification_quantity'][trim($detail)] * $order_quantity) }}
                    @endif
                    <br>
                @endforeach
            </td>
{{--                @else--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                @endif--}}
{{--            @else--}}
{{--                @php--}}
{{--                    $designationKey = (int)$key;--}}
{{--                @endphp--}}

{{--                @if(!empty($result['designation_id'][$designationKey]))--}}
{{--                    <td>--}}
{{--                        @if(isset($result['designation_id'][$designationKey][$item['detail']]))--}}
{{--                            @foreach($result['designation_id'][$designationKey][$item['detail']] as $record)--}}
{{--                                {{ $record['item']->material_issuance_id }}@if(!$loop->last), @endif--}}
{{--                            @endforeach--}}
{{--                        @else--}}
{{--                            @foreach($details as $detail)--}}
{{--                                @foreach($result['designation_id'][$designationKey][$detail] ?? [] as $record)--}}
{{--                                    {{ $record['item']->material_issuance_id }}@if(!$loop->last), @endif--}}
{{--                                @endforeach--}}
{{--                                <br>--}}
{{--                            @endforeach--}}
{{--                        @endif--}}
{{--                    </td>--}}
{{--                    <td>--}}
{{--                        @if(isset($result['designation_id'][$designationKey][$item['detail']]))--}}
{{--                            @foreach($result['designation_id'][$designationKey][$item['detail']] as $record)--}}
{{--                                {{ $record['quantity'] }}@if(!$loop->last), @endif--}}
{{--                            @endforeach--}}
{{--                        @else--}}
{{--                            @foreach($details as $detail)--}}
{{--                                @foreach($result['designation_id'][$designationKey][$detail] ?? [] as $record)--}}
{{--                                    {{ $record['quantity'] }}@if(!$loop->last), @endif--}}
{{--                                @endforeach--}}
{{--                                <br>--}}
{{--                            @endforeach--}}
{{--                        @endif--}}
{{--                    </td>--}}
{{--                    <td>--}}
{{--                        @if(isset($result['designation_id'][$designationKey][$item['detail']]))--}}
{{--                            @foreach($result['designation_id'][$designationKey][$item['detail']] as $record)--}}
{{--                                {{ $record['item']->quantity }}@if(!$loop->last), @endif--}}
{{--                            @endforeach--}}
{{--                        @else--}}
{{--                            @foreach($details as $detail)--}}
{{--                                @foreach($result['designation_id'][$designationKey][$detail] ?? [] as $record)--}}
{{--                                    {{ $record['item']->quantity }}@if(!$loop->last), @endif--}}
{{--                                @endforeach--}}
{{--                                <br>--}}
{{--                            @endforeach--}}
{{--                        @endif--}}
{{--                    </td>--}}
{{--                @else--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                @endif--}}
{{--            @endif--}}
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
