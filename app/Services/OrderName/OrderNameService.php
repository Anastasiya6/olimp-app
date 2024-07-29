<?php

namespace App\Services\OrderName;
use App\Models\Designation;
use App\Models\Material;
use App\Models\Order;
use App\Models\OrderName;
use Illuminate\Http\Request;

class OrderNameService
{

    public function store(Request $request)
    {
        OrderName::create([
            'name' => $request->name,
            'is_order' => $request->is_order,
        ]);

    }

    public function update(Request $request, OrderName $orderName)
    {
        $orderName->name = $request->name;
        $orderName->is_order = $request->is_order;
        $orderName->save();

            return 1;
    }
}
