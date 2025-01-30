<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\NotInApplicationStatementService;

class NotInApplicationStatement extends Controller
{
    public function notInApplicationStatement($sender_department, $order_name_id,  NotInApplicationStatementService $service){

        $service->notInApplicationStatement( $sender_department, $order_name_id);

    }

}
