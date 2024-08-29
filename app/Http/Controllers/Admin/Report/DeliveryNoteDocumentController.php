<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\DeliveryNoteService;

class DeliveryNoteDocumentController extends Controller
{
    public function deliveryNote($sender_department,$receiver_department,$order_name_id,$document_date,DeliveryNoteService $service)
    {
       // dd($sender_department,$receiver_department,$order_name_id,$document_date);
        $service->deliveryNote($sender_department,$receiver_department,$order_name_id,$document_date);
    }
}
