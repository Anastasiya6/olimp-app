<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Reports\EntryDetailService;

class EntryDetailController extends Controller
{
    public function entryDetail(EntryDetailService $service)
    {
        $order = Order::first();
        if($order){
            $service->entryDetail($order->designation->designation);
        }

    }
}
