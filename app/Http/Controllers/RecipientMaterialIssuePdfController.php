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
        $validated = $request->validate([
            'recipient' => ['required', 'integer', 'min:1'],
            'posted_only' => ['sometimes', 'boolean'],
        ]);

        return $this(User::findOrFail($validated['recipient']), $service);
    }

    public function __invoke(User $recipient, OrderMaterialIssueService $service)
    {
        $items = MaterialIssuanceItem::with('importMaterial.unit')
            ->whereIn('material_issuance_id', MaterialIssuance::query()
                ->select('id')
                ->where('received_by_user_id', $recipient->id)
                ->when(request()->boolean('posted_only'), fn ($query) => $query->where('status', 'posted')))
            ->orderBy('material_issuance_id')
            ->get();

        return Pdf::loadView('pdf.recipient-material-issue', [
            'recipient' => $recipient,
            'rows' => $service->summarize($items),
            'generatedAt' => now(),
            'postedOnly' => request()->boolean('posted_only'),
        ])->setPaper('a4', 'landscape')->stream('recipient-material-issue-'.$recipient->id.'.pdf');
    }
}
