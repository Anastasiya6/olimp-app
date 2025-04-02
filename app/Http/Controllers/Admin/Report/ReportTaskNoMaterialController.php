<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\TaskNoMaterialService;
use App\Services\Reports\WriteOffNoMaterialService;
use App\Services\Reports\WriteOffService;
use Illuminate\Http\Request;

class ReportTaskNoMaterialController extends Controller
{
    public function task(Request $request, TaskNoMaterialService $service)
    {
        $service->taskNoMaterial($request->sender_department);
    }
}
