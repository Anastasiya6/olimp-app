<?php

namespace App\Http\Controllers;

use App\Models\MaterialIssuance;
use App\Models\MaterialIssuanceItem;
use App\Models\OrderName;
use App\Services\Reports\OrderMaterialIssueService;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderMaterialIssuePdfController extends Controller
{
    public function __invoke(OrderName $order, OrderMaterialIssueService $service)
    {
        $dates = request()->validate([
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d'],
        ]);
        $dateFrom = $dates['date_from'] ?? null;
        $dateTo = $dates['date_to'] ?? null;

        if ($dateFrom && $dateTo && $dateTo < $dateFrom) {
            abort(422, 'Кінцева дата має бути не раніше початкової.');
        }

        $items = MaterialIssuanceItem::with('importMaterial.unit')
            ->whereIn('material_issuance_id', MaterialIssuance::query()
                ->select('id')
                ->where('order_name_id', $order->id)
                ->when(request()->boolean('posted_only'), fn ($query) => $query->where('status', 'posted'))
                ->when($dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo, fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
                )
            ->orderBy('material_issuance_id')
            ->get();

        return Pdf::loadView('pdf.order-material-issue', [
            'order' => $order,
            'rows' => $service->summarize($items),
            'generatedAt' => now(),
            'postedOnly' => request()->boolean('posted_only'),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ])->setPaper('a4', 'landscape')->stream('order-material-issue-'.$order->id.'.pdf');
    }
}
