<?php

namespace App\Services\MaterialPurchase;
use App\Models\MaterialPurchase;
use Illuminate\Http\Request;

class MaterialPurchaseService
{

    public function store(Request $request)
    {
        MaterialPurchase::create([
            'designation_id' => $request->designation_id,
            'designation_entry_id' => $request->designation_entry_id,
            'material_id' => $request->material_id,
            'norm' => $request->norm,
            'code_1c' => $request->code_1c
        ]);
    }

    public function update(Request $request, MaterialPurchase $material_purchase)
    {
        $material_purchase->material_id = $request->material_id;
        $material_purchase->norm = $request->norm;
        $material_purchase->code_1c = $request->code_1c;

        $material_purchase->save();
    }
}
