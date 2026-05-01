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

<h2>Звіт видачі матеріалів #{{ $document->id }}</h2>

<p><strong>Замовлення: №</strong> {{ $document->order_name->name }}</p>

<p><strong>Дата:</strong> {{ $document->created_at }}</p>

<p><strong>Отримав:</strong>
    {{ $document->issued_to_employee ?? '—' }}
</p>

<p><strong>Відпустив:</strong>
    {{ $document->issued_by_employee ?? '—' }}
</p>

<p><strong>Деталь (на що брали):</strong>
    {{ $document->designation->designation.' '.$document->designation->name ?? '—' }}
</p>

<table>
    <thead>
    <tr>
        <th>Деталь</th>
        <th>Матеріал</th>
        <th>Матеріал 1С</th>
        <th>Кількість</th>
        <th>Факт кількість</th>
        <th>од.</th>
    </tr>
    </thead>

    <tbody>
    @foreach($document->items as $item)
        <tr>
            <td>{{ $item->details }}</td>
            <td>{{ $item->material->name ?? $item->designation->name ?? '—' }}</td>
            <td>{{ $item->importMaterial->name ?? '—' }}</td>
            <td>{{ $item->quantity ?? 0 }}</td>
            <td>{{ $item->fact_quantity ?? 0 }}</td>
            <td>{{ $item->importMaterial->unit->unit ?? '—' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
