<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\DeliveryNotePlanService;

class DeliveryNotePlanController extends Controller
{
    public function deliveryNotePlan($sender_department,$receiver_department,$order_name_id,$type_report_in,DeliveryNotePlanService $service)
    {
        $fileName = $service->deliveryNote($sender_department,$receiver_department,$order_name_id,$type_report_in);

        if($type_report_in == 'Excel'){

            return response()->download($fileName)->deleteFileAfterSend(true);

        }
    }
}
