<?php

namespace App\Http\Controllers;

use App\Models\MaterialIssuance;
use App\Models\MaterialIssuanceItem;
use App\Models\User;
use App\Services\Reports\OrderMaterialIssueService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RecipientMaterialIssuePdfController extends Controller
{
    public function generate(Request $request, OrderMaterialIssueService $service)
    {
        $validated = $request->validate(['recipient' => ['required', 'integer', 'min:1']]);

        return $this(User::findOrFail($validated['recipient']), $service);
    }

    public function __invoke(User $recipient, OrderMaterialIssueService $service)
    {
        $items = MaterialIssuanceItem::with('importMaterial.unit')
            ->whereIn('material_issuance_id', MaterialIssuance::query()
                ->select('id')
                ->where('received_by_user_id', $recipient->id))
            ->orderBy('material_issuance_id')
            ->get();

        return Pdf::loadView('pdf.recipient-material-issue', [
            'recipient' => $recipient,
            'rows' => $service->summarize($items),
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape')->stream('recipient-material-issue-'.$recipient->id.'.pdf');
    }
}
