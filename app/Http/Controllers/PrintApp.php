<?php

namespace App\Http\Controllers;

use App\Services\Statements\ApplicationStatementPrintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;

class PrintApp extends Controller
{
    public function print()
    {
        $queryResult =  ApplicationStatementPrintService::queryAppStatement(1);

        $pdf = new Mpdf();
        $view = View::make('public::include.print.statement')->render();
        $pdf->WriteHTML($view);

         $pdf->Output('output.pdf', 'I');
    }

}
