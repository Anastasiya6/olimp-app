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
        $items = MaterialIssuanceItem::with('importMaterial.unit')
            ->whereIn('material_issuance_id', MaterialIssuance::query()
                ->select('id')
                ->where('order_name_id', $order->id)
                ->when(request()->boolean('posted_only'), fn ($query) => $query->where('status', 'posted'))
                )
            ->orderBy('material_issuance_id')
            ->get();

        return Pdf::loadView('pdf.order-material-issue', [
            'order' => $order,
            'rows' => $service->summarize($items),
            'generatedAt' => now(),
            'postedOnly' => request()->boolean('posted_only'),
        ])->setPaper('a4', 'landscape')->stream('order-material-issue-'.$order->id.'.pdf');
    }
}
