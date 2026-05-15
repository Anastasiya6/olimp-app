<?php

namespace App\Http\Controllers;

use App\Models\MaterialIssuance;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class IssuanceMaterialBulkPdfController extends Controller
{
    public function bulkPdf(Request $request)
    {

        $ids = explode(',', $request->ids);

        $documents = MaterialIssuance::with( 'items.material', 'items.importMaterial')->whereIn('id', $ids)->get();

        $pdf = Pdf::loadView('pdf.issuance-materials', [
            'documents' => $documents
        ]);

        return $pdf->stream("issuance.pdf");
    }
}
