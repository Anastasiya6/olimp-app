<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\WriteOffService;

class ReportWriteOffController extends Controller
{
    public function writeOff($ids,$order_number,$start_date,$end_date,$sender_department,$receiver_department,$type_report,WriteOffService $service)
    {
        $service->writeOff($ids,$order_number,$start_date,$end_date,$sender_department,$receiver_department,$type_report);
    }
}
