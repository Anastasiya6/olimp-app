<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Загальна відомість залишків — {{ $department->number }}</title>
    <style>
        @page { margin: 28px; }
        body { color: #111; font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        h1 { margin: 0 0 12px; font-size: 17px; }
        p { margin: 6px 0; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 16px; }
        .report-table + .report-table { page-break-before: always; margin-top: 16px; }
        th, td { border: 1px solid #777; padding: 6px; vertical-align: top; }
        th { background: #eee; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        .number { text-align: right; white-space: nowrap; }
        .center { text-align: center; }
        .empty { text-align: center; padding: 18px; }
    </style>
</head>
<body>
    <h1>Загальна відомість залишків по складах</h1>
    <p><strong>Склад:</strong> {{ $department->number }}</p>
    <p>Сформовано: {{ $generatedAt->format('d.m.Y H:i') }}</p>

    @forelse($materials->chunk(100) as $chunk)
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%">№</th>
                    <th style="width: 10%">Код 1С</th>
                    <th style="width: 12%">Артикул</th>
                    <th style="width: 48%">Матеріал</th>
                    <th style="width: 15%" class="number">Залишок</th>
                    <th style="width: 10%" class="center">Од. виміру</th>
                </tr>
            </thead>
            <tbody>
                @foreach($chunk as $material)
                    <tr>
                        <td class="center">{{ $loop->parent->iteration * 100 - 100 + $loop->iteration }}</td>
                        <td>{{ $material->code ?? '—' }}</td>
                        <td>{{ $material->article ?? '—' }}</td>
                        <td>{{ $material->name }}</td>
                        <td class="number">{{ $material->report_balance }}</td>
                        <td class="center">{{ $material->unit?->unit ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @empty
        <table class="report-table">
            <tbody>
                <tr>
                    <td class="empty">Для вибраного складу залишків немає.</td>
                </tr>
            </tbody>
        </table>
    @endforelse
</body>
</html>
