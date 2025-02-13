<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\HelpService\PDFService;
use App\Services\Reports\ApplicationStatementService;
use App\Services\Statements\ApplicationStatementPrintService;
use Illuminate\Http\Request;

class ApplicationStatementController extends Controller
{
    public function applicationStatement($filter,$order_number,$department,applicationStatementService $service)
    {
        $service->applicationStatement($filter,$order_number,$department);
    }
}
