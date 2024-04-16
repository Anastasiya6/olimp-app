<?php

namespace App\Http\Controllers\Pub;

use App\Http\Controllers\Controller;
use App\Services\Statements\ApplicationStatementPrintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Mpdf\Mpdf;

class PrintController extends Controller
{
    public function printPage()
    {
        $queryResult =  ApplicationStatementPrintService::queryAppStatement(1);

        return view('public::include.print.statement')->with('data', $queryResult);
    }
}
