<?php

namespace App\Services\DesignationMaterial;
use App\Models\DesignationMaterial;
use Illuminate\Http\Request;

class DesignationMaterialService
{

    public function store(Request $request)
    {
        DesignationMaterial::updateOrCreate([
            'designation_id' => $request->designation_id,
            'material_id' => $request->material_id
            ],
            [
                'norm' => $request->norm,
                'department_id' => $request->department_id
        ]);
    }

    public function update(Request $request, DesignationMaterial $designationMaterial)
    {
        $designationMaterial->norm = $request->norm;

        $designationMaterial->save();
    }
}
