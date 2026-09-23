<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Видача матеріалів отримувачу {{ $recipient->name }}</title>
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
    <h1>Звіт видачі матеріалів по отримувачу</h1>
    <p><strong>Отримувач:</strong> {{ $recipient->name }}</p>
    <p>За весь час. Усі замовлення та ручні видачі. Усі документи, включно з непроведеними.</p>
    <p>Сформовано: {{ $generatedAt->format('d.m.Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th style="width: 10%">Код 1С</th>
                <th style="width: 12%">Артикул 1С</th>
                <th style="width: 34%">Матеріал</th>
                <th style="width: 7%">Од. вим.</th>
                <th style="width: 13%">Видано всього</th>
                <th style="width: 24%">Документи видачі</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row['code'] }}</td>
                    <td>{{ $row['article'] }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['unit'] }}</td>
                    <td class="quantity">{{ rtrim(rtrim(number_format($row['quantity'], 6, ',', ' '), '0'), ',') }}</td>
                    <td>{{ implode(', ', $row['documents']) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty">Для цього отримувача немає матеріалів у документах видачі.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
