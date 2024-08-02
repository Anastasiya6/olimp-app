<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\WriteOffNoMaterialService;
use App\Services\Reports\WriteOffService;

class ReportWriteOffNoMaterialController extends Controller
{
    public function writeOffNoMaterial($order_number, $start_date, $end_date, $sender_department, $receiver_department, WriteOffNoMaterialService $service)
    {
        $service->writeOffNoMaterial($order_number,$start_date,$end_date,$sender_department,$receiver_department);
    }
}
