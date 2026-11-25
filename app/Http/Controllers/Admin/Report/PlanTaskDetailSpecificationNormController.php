<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanTaskReportRequest;
use App\Services\Reports\PlanTaskDetailSpecificationNormService;

class PlanTaskDetailSpecificationNormController extends Controller
{
    public function planTaskDetailSpecificationNorm($order_name_id,$sender_department,$receiver_department,$type_report_in,$with_purchased,$with_material_purchased,PlanTaskDetailSpecificationNormService $service, PlanTaskReportRequest $request)
    {
        $service->detailSpecificationNorm($order_name_id,$sender_department,$receiver_department,$type_report_in,$with_purchased,$with_material_purchased,$request->sectionId());
    }
}
