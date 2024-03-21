<?php

namespace App\Services\Material;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialService
{

    public function store(Request $request)
    {
        Material::create([
            'name' => $request->name,
            'type_unit_id' => $request->type_unit_id,
        ]);
    }

    public function update(Request $request, Material $material)
    {
        $material->name = $request->name;
        $material->type_unit_id = $request->type_unit_id;

        $material->save();
    }
}
