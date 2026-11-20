<?php

namespace App\Http\Controllers;

use App\Models\MaterialReturn;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MaterialReturnReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate(['posted_only' => ['sometimes', 'boolean']]);
        $postedOnly = $request->boolean('posted_only');

        $query = MaterialReturn::query()
            ->when($postedOnly, fn ($query) => $query->where('status', 'posted'));

        $documents = (clone $query)->orderBy('id')
            ->get(['id', 'import_material_id'])
            ->groupBy('import_material_id');

        $rows = (clone $query)
            ->with('material.unit')
            ->select('import_material_id')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->groupBy('import_material_id')
            ->get()
            ->each(function ($row) use ($documents) {
                $row->documents = $documents->get($row->import_material_id, collect())->pluck('id')->all();
            })
            ->sortBy(fn ($row) => $row->material?->article)
            ->values();

        return Pdf::loadView('pdf.material-return', [
            'rows' => $rows,
            'postedOnly' => $postedOnly,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape')->stream('material-returns.pdf');
    }
}
