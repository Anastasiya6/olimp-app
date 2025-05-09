<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\TaskService;
use Illuminate\Http\Request;

class ReportTaskController extends Controller
{
    public function task(Request $request,TaskService $service)
    {
        $fileName = $service->task($request);

        if($request->type_report_in == 'Excel'){

            return response()->download($fileName)->deleteFileAfterSend(true);

        }
    }
}
