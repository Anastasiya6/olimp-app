<?php

namespace App\Services\HelpService;

use App\Models\Purchase;
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

            $array_materials = $this->checkMaterial($item->designationMaterial,$item->designation_id,$item->designation_id,$item->with_purchased,1);

            $this->fillMaterials($item->materials,$item->designation->designation,$item->quantity,$array_materials);

            $this->node($item->materials,$item->designation_id,$item->quantity,$item->with_purchased);

        }

        $records->materials = $this->all_materials;

        return $this->sortByGroup($records);
    }

    public function getTypeMaterial($type)
    {
        if($type == 'purchase'){

            return ["", 1];

        }else{

            return ["* 1.2",1.2];

        }
    }

    private function checkMaterial($designationMaterial,$designation_id, $designation_entry_id, $with_purchased,$quantity)
    {
        if($with_purchased == 1) {

            $purchase = $this->getPurchase($designation_id, $designation_entry_id,$quantity);

            if(!empty($purchase)){

                return $purchase;
            }
        }

        return $this->getMaterial($designationMaterial,$quantity);

    }

    private function getMaterial($designationMaterial,$quantity)
    {
        $array = array();
        if($designationMaterial->isNotEmpty()) {

            foreach ($designationMaterial as $material) {
                $array[] = [
                    'type' => 'material',
                    'material_id' => $material->material->id,
                    'material' => $material->material->name,
                    'norm' => $material->norm,
                    'quantity' => $quantity,
                    'unit' => $material->material->unit->unit ?? "",
                    'code_1c' => $material->material->code_1c
                ];

            }

            return $array;
        }

    }

    private function getPurchase($designation_id, $designation_entry_id,$quantity)
    {
        $purchase = Purchase::where('designation_id', $designation_id)->where('designation_entry_id', $designation_entry_id)->first();

        if ($purchase) {

            return array([
                'type' => 'purchase',
                'material_id' => $purchase->purchase,
                'material' => $purchase->purchase,
                'norm' => 1,
                'quantity' => $quantity,
                'unit' => '',
                'code_1c' => ''
            ]);
        }

        return array();
    }
    private function node($materials,$designation_id,$quantity,$with_purchased)
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
                            'type' => $type,
                            'detail' => $specification->designationEntry->designation,
                            'material' => $specification->designationEntry->designation,
                            'norm' => $specification->quantity,
                            'unit' => $type == 'kr' ? 'шт' : $specification->designationEntry->unit->unit ?? "",
                            'sort' => $type == 'kr' ? 1 : 2
                        ]);
                    }elseif($this->type_report == 1) {
                        $this->all_materials[] = array(
                            'type' => $type,
                            'detail' => $specification->designationEntry->designation,
                            'material_id' => $specification->designationEntry->id . $type,
                            'material' => $specification->designationEntry->designation,
                            'norm' => $specification->quantity,
                            'quantity_norm_quantity_detail' => $specification->quantity * $quantity,
                            'code_1c' => '',
                            'unit' => $type == 'kr' ? 'шт' : $specification->designationEntry->unit->unit ?? "",
                            'sort' => $type == 'kr' ? 1 : 2);
                       // dd($this->all_materials);
                    }
                }

                $array_material = $this->checkMaterial($specification->designationMaterial,$specification->designation_id,$specification->designation_entry_id,$with_purchased,$specification->quantity);

                $this->fillMaterials($materials,$specification->designations->designation,$quantity,$array_material);

                $this->node($materials,$specification->designation_entry_id,$quantity,$with_purchased);

            }
        }
        return $materials;
    }

    private function fillMaterials($materials,$designation,$quantity,$array_materials)
    {
        if (empty($array_materials)) {
            return;
        }

        foreach ($array_materials as $array_material){

            if ($this->type_report == 1) {

                $this->all_materials[] = array(
                    'type' => $array_material['type'],
                    'detail' => $designation,
                    'material' => $array_material['material'],
                    'material_id' => $array_material['material_id'],
                    'norm' => $array_material['norm'],
                    'quantity_norm' => $quantity * $array_material['norm'],
                    'quantity_norm_quantity_detail' => $array_material['quantity'] * $quantity * $array_material['norm'],
                    'unit' => $array_material['unit'] ?? "",
                    'code_1c' => $array_material['code_1c'],
                    'sort' => 0);
            } elseif ($this->type_report == 0) {

                $materials->push((object)[
                    'type' => $array_material['type'],
                    'detail' => $designation,
                    'material' => $array_material['material'],
                    'quantity' => $array_material['quantity'],
                    'norm' => $array_material['norm'],
                    'unit' => $array_material['unit'] ?? "",
                    'sort' => 0
                ]);
            }
        }

        return $materials;
    }

    private function sortByGroup($records)
    {
        if($this->type_report == 1) {

            $records = collect($records->materials);

            if($this->type_group == 'material_id'){

                return $records->groupBy('material_id')->map(function ($group) {
                    return [
                        'type' => $group->first()['type'],
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

            }elseif($this->type_group == 'detail'){

                return $records->groupBy('material')->map(function ($group) {
                    // Группируем внутри каждой группы по названию материала
                    //return $group->groupBy('material_name')->map(function ($materialDetails, $materialName) {
                    // Группируем далее по наименованию детали
                    return $group->groupBy('detail')->map(function ($details) {
                        // Возвращаем детали для каждой детали в группе
                        return [
                            'id' => $details->first()['material_id'],
                            'type' => $details->first()['type'],
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
}
