<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\PlanTaskSpecificationNormService;

class PlanTaskSpecificationNormController extends Controller
{
    public function planTaskSpecificationNorm($order_name_id,$sender_department,$receiver_department,$type_report_in,$with_purchased,$with_material_purchased,PlanTaskSpecificationNormService $service)
    {
        $fileName = $service->specificationNorm($order_name_id,$sender_department,$receiver_department,$type_report_in,$with_purchased,$with_material_purchased);

        if($type_report_in == 'Excel'){

            return response()->download($fileName)->deleteFileAfterSend(true);

        }
    }
}
