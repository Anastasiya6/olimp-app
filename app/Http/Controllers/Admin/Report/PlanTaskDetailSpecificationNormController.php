<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\PlanTaskDetailSpecificationNormService;
use App\Services\Reports\PlanTaskSpecificationNormService;

class PlanTaskDetailSpecificationNormController extends Controller
{
    public function planTaskDetailSpecificationNorm($order_name_id,$department,$type_report_in,PlanTaskDetailSpecificationNormService $service)
    {
        $fileName = $service->detailSpecificationNorm($order_name_id,$department,$type_report_in);
    }
}
