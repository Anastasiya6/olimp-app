<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Reports\EntryDetailService;

class EntryDetailController extends Controller
{
    public function entryDetail($order_number,EntryDetailService $service)
    {
        $order = Order::where('order_number',$order_number)->first();
        if($order){
            $service->entryDetail($order->designation->designation,$order_number);
        }

    }
}
