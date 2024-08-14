<?php

namespace App\Services\HelpService;

use App\Models\Specification;

class MaterialService
{
    public array $all_materials;

    public int $type_report;

    public $type_group = null;

    public function material($records,$type_report,$type_group = null)
    {
        $this->type_report = $type_report;

        $this->all_materials = [];

        $this->type_group = $type_group;

        foreach($records as $item){

            $item->materials = collect();

            if($item->designationMaterial->isNotEmpty()){

                foreach($item->designationMaterial as $material) {

                    if($this->type_report == 0){

                        $item->materials->push((object)[
                            'detail' => $item->designation->designation,
                            'material' => $material->material->name,
                            'norm' => $material->norm,
                            'quantity' => 1,//$item->quantity,
                            'unit' => $material->material->unit->unit??"",
                            'sort' => 0
                        ]);

                    }elseif($this->type_report == 1){

                        $this->all_materials[] = array(
                            'detail' => $item->designation->designation,
                            'material_id' => $material->material->id,
                            'material' => $material->material->name,
                            'norm' => $material->norm,
                            'quantity' => $item->quantity,
                            'quantity_norm' => $item->quantity * $material->norm,
                            'quantity_norm_quantity_detail' => $item->quantity * $material->norm,
                            'unit' => $material->material->unit->unit??'',
                            'code_1c' => $material->material->code_1c,
                            'sort' => 0);
                    }
                }
            }

            $this->node($item->materials,$item->designation_id,$item->quantity);

        }

        $records->materials = $this->all_materials;

        if($this->type_report == 1) {

            $records = collect($records->materials);

            if($type_group == 'material_id'){
                return $records->groupBy('material_id')->map(function ($group) {
                    return [
                        'material_id' => $group->first()['material_id'],
                        'code_1c' => $group->first()['code_1c'],
                        'material' => $group->first()['material'],
                        'norm' => $group->sum('norm'),
                        'quantity_norm' => $group->sum('quantity_norm'),
                        'quantity_norm_quantity_detail' => $group->sum('quantity_norm_quantity_detail'),
                        'unit' => $group->first()['unit'],
                        'sort' => $group->first()['sort'],
                    ];
                })->sortBy('material')->sortBy('sort');
            }elseif($type_group == 'detail'){

                return $records->groupBy('material')->map(function ($group) {
                    // Группируем внутри каждой группы по названию материала
                    //return $group->groupBy('material_name')->map(function ($materialDetails, $materialName) {
                    // Группируем далее по наименованию детали
                    return $group->groupBy('detail')->map(function ($details) {
                        // Возвращаем детали для каждой детали в группе
                        return [
                            'id' => $details->first()['material_id'],
                            'material' => $details->first()['material'],
                            'detail' => $details->first()['detail'],
                            'quantity_norm' => $details->sum('quantity_norm'),
                            'unit' => $details->first()['unit'],
                            'norm' => $details->first()['norm'],
                            'sort' => $details->first()['sort'],
                        ];
                    })->sortBy('detail'); // Сортировка по 'detail' внутри группы
                })->sortKeys();
            }
        }

        return $records;
    }

    private function node($materials,$designation_id,$quantity)
    {
        $specifications = Specification
            ::where('designation_id', $designation_id)
            ->with(['designations', 'designationEntry', 'designationMaterial.material.unit'])
            ->get();

        if ($specifications->isNotEmpty()) {

            foreach ($specifications as $specification) {

                if (str_starts_with($specification->designationEntry->designation, 'КР') || str_starts_with($specification->designationEntry->designation, 'ПИ0')) {
                    if($this->type_group == 'detail'){
                        continue;
                    }
                    $type = str_starts_with($specification->designationEntry->designation, 'КР') ? 'kr' : 'pki';

                    if($this->type_report == 0) {
                        $materials->push((object)[
                            'detail' => $specification->designationEntry->designation,
                            'material' => $specification->designationEntry->designation,
                            'norm' => $specification->quantity,
                            'unit' => $type == 'kr' ? 'шт' : $specification->designationEntry->unit->unit ?? "",
                            'sort' => $type == 'kr' ? 1 : 2
                        ]);
                    }elseif($this->type_report == 1) {
                        $this->all_materials[] = array(
                            'detail' => $specification->designationEntry->designation,
                            'material_id' => $specification->designationEntry->id . $type,
                            'material' => $specification->designationEntry->designation,
                            'norm' => $specification->quantity,
                            'code_1c' => '',
                            'unit' => $type == 'kr' ? 'шт' : $specification->designationEntry->unit->unit ?? "",
                            'sort' => $type == 'kr' ? 1 : 2);
                    }
                }

                foreach ($specification->designationMaterial as $material) {

                    if($this->type_report == 1) {
                        $this->all_materials[] = array(
                            'detail' => $specification->designations->designation,
                            'material_id' => $material->material->id,
                            'material' => $material->material->name,
                            'norm' => $material->norm,
                            'quantity_norm' => $quantity * $material->norm,
                            'quantity_norm_quantity_detail' => $specification->quantity * $quantity * $material->norm,
                            'unit' => $material->material->unit->unit ?? "",
                            'code_1c' => $material->material->code_1c,
                            'sort' => 0);
                    }elseif($this->type_report == 0) {
                        $materials->push((object)[
                            'detail' => $specification->designations->designation,
                            'material' => $material->material->name,
                            'quantity' => $specification->quantity,
                            'norm' => $material->norm,
                            'unit' => $material->material->unit->unit ?? "",
                            'sort' => 0
                        ]);
                    }
                }

                $this->node($materials,$specification->designation_entry_id,$quantity);

            }
        }
        return $materials;
    }
}
