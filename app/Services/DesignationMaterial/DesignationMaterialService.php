<?php

namespace App\Services\DesignationMaterial;
use App\Models\DesignationMaterial;
use Illuminate\Http\Request;

class DesignationMaterialService
{

    public function store(Request $request)
    {
        $norm = $this->getNorm( $request->norm);
        DesignationMaterial::updateOrCreate([
            'designation_id' => $request->designation_id,
            'material_id' => $request->material_id
            ],
            [
                'norm' => $norm,
                'department_id' => $request->department_id
        ]);
    }

    public function update(Request $request, DesignationMaterial $designationMaterial)
    {
        $norm = $this->getNorm( $request->norm);
        $designationMaterial->norm = $norm;
        $designationMaterial->department_id = $request->department_id;
        $designationMaterial->save();
    }

    private function getNorm($norm)
    {
        // Проверяем, содержит ли $norm запятую
        if (strpos($norm, ',') !== false) {
            // Если да, заменяем запятую на точку
            $norm = str_replace(',', '.', $norm);
        }
        $norm = floatval($norm);
        return $norm;
    }
}
