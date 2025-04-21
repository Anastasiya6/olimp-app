<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\PlanTaskDetailSpecificationNormService;
use App\Services\Reports\PlanTaskSpecificationNormService;

class PlanTaskDetailSpecificationNormController extends Controller
{
    public function planTaskDetailSpecificationNorm($order_name_id,$sender_department,$receiver_department,$type_report_in,$with_purchased,$with_material_purchased,PlanTaskDetailSpecificationNormService $service)
    {
        $fileName = $service->detailSpecificationNorm($order_name_id,$sender_department,$receiver_department,$type_report_in,$with_purchased,$with_material_purchased);
    }
}
