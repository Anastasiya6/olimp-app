<?php

namespace App\Http\Controllers;

use App\Models\MaterialIssuance;
use Barryvdh\DomPDF\Facade\Pdf;

class IssuanceMaterialPdfController extends Controller
{
    public function show($id)
    {
        $document = MaterialIssuance::with('items.material', 'items.importMaterial')->findOrFail($id);

        $pdf = Pdf::loadView('pdf.issuance-material', [
            'document' => $document
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
