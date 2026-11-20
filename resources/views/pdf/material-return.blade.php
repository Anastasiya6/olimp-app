<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Повернення матеріалів — підсумок за матеріалами</title>
    <style>
        @page { margin: 28px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        h1 { font-size: 17px; margin: 0 0 12px; }
        p { margin: 6px 0; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 16px; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        th, td { border: 1px solid #777; padding: 6px; vertical-align: top; overflow-wrap: break-word; }
        th { background: #eeeeee; }
        .quantity { text-align: right; white-space: nowrap; }
        .empty { text-align: center; padding: 18px; }
    </style>
</head>
<body>
    <h1>Повернення матеріалів — підсумок за матеріалами</h1>
    <p>За весь час, за всіма цехами.</p>
    <p>{{ $postedOnly ? 'Лише проведені документи.' : 'Усі документи, включно з непроведеними.' }}</p>
    <p>Сформовано: {{ $generatedAt->format('d.m.Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th style="width: 15%">Артикул</th>
                <th style="width: 35%">Матеріал</th>
                <th style="width: 10%">Од. вим.</th>
                <th style="width: 15%">Повернено всього</th>
                <th style="width: 25%">Документи повернення</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row->material?->article ?? '—' }}</td>
                    <td>{{ $row->material?->name ?? '—' }}</td>
                    <td>{{ $row->material?->unit?->unit ?? '—' }}</td>
                    <td class="quantity">{{ number_format($row->total_quantity, 3, ',', ' ') }}</td>
                    <td>{{ implode(', ', $row->documents) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">За вибраними умовами повернень матеріалів немає.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
