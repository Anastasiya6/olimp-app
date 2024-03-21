<?php

namespace App\Services\Order;
use App\Models\Designation;
use App\Models\Material;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderService
{

    public function store(Request $request)
    {
        $designation = Designation::where('designation',$request->designation)->first();

        if(isset($designation->id)) {
            Order::create([
                'order_number' => $request->order_number,
                'designation_id' => $designation->id,
                'quantity' => $request->quantity,
            ]);
        }
    }

    public function update(Request $request, Order $order)
    {
        $designation = Designation::where('designation',$request->designation)->first();

        if(isset($designation->id)){
            $order->order_number = $request->order_number;
            $order->designation_id = $designation->id;
            $order->quantity = $request->quantity;
            $order->save();
            return 1;
        }
        return 0;

    }
}
