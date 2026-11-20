<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\MaterialIssuance;
use Barryvdh\DomPDF\Facade\Pdf;

class IssuanceMaterialPdfController extends Controller
{
    public function show($id)
    {
        $document = MaterialIssuance::with('items.material', 'items.designation', 'items.importMaterial.unit')->findOrFail($id);

        $detailNumbersByItem = $document->items->mapWithKeys(fn ($item) => [
            $item->id => collect(explode(',', (string) $item->details))
                ->map(fn ($number) => trim($number))
                ->filter(fn ($number) => $number !== ''),
        ]);
        $detailNames = Designation::whereIn('designation', $detailNumbersByItem->flatten()->unique())
            ->orderBy('id')
            ->get(['designation', 'name'])
            ->unique('designation')
            ->pluck('name', 'designation');

        $pdf = Pdf::loadView('pdf.issuance-material', [
            'document' => $document,
            'detailNumbersByItem' => $detailNumbersByItem,
            'detailNames' => $detailNames,
        ]);

        return $pdf->stream("issuance-{$document->id}.pdf");
    }

    public function manualShow($id)
    {
        $document = MaterialIssuance::with('items.material', 'items.importMaterial.unit')->findOrFail($id);

        $pdf = Pdf::loadView('pdf.manual-issuance-material', [
            'document' => $document
        ]);

        return $pdf->stream("issuance-{$document->id}.pdf");
    }
}
