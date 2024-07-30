<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\DeliveryNoteService;

class DeliveryNoteController extends Controller
{
    public function deliveryNote($sender_department,$receiver_department,$order_name_id,DeliveryNoteService $service)
    {
        $service->deliveryNote($sender_department,$receiver_department,$order_name_id);
    }
}
