<?php

namespace App\Services\Purchase;
use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseService
{

    public function store(Request $request)
    {
       // dd($request);
        Purchase::create([
            'designation_id' => $request->designation_id,
            'designation_entry_id' => $request->designation_entry_id,
            'quantity' => $request->quantity,
            'purchase' => $request->purchase
        ]);
    }

    public function update(Request $request, Purchase $purchase)
    {
        $purchase->quantity = $request->quantity;
        $purchase->purchase = $request->purchase;
        //$purchase->designation_id = $request->designation;
        //$purchase->designation_entry_id = $request->designation_entry;

        $purchase->save();
    }
}
