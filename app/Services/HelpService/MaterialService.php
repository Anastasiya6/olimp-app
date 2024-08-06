<?php

namespace App\Services\HelpService;

use App\Models\Specification;

class MaterialService
{
    public array $all_materials;

    public int $type_report;

    public function material($records,$type_report)
    {
        $this->type_report = $type_report;

        $this->all_materials = [];

        foreach($records as $item){

            $item->materials = collect();

            if($item->designationMaterial->isNotEmpty()){

                foreach($item->designationMaterial as $material) {

                    if($this->type_report == 0){

                        $item->materials->push((object)[
                            'material' => $material->material->name,
                            'norm' => $material->norm,
                            'unit' => $material->material->unit->unit??"",
                            'sort' => 0
                        ]);

                    }elseif($this->type_report == 1){

                        $this->all_materials[] = array(
                            'material_id' => $material->material->id,
                            'material' => $material->material->name,
                            'norm' => $material->norm,
                            'unit' => $material->material->unit->unit??'',
                            'code_1c' => $material->material->code_1c,
                            'sort' => 0);
                    }
                }
            }
            $this->node($item->materials,$item->designation_id);

        }
        $records->materials = $this->all_materials;

        if($this->type_report == 1) {

            $records->materials = collect($records->materials);

            $records->materials = $records->materials->groupBy('material_id')->map(function ($group) {
                return [
                    'material_id' => $group->first()['material_id'],
                    'code_1c' => $group->first()['code_1c'],
                    'material' => $group->first()['material'],
                    'norm' => $group->sum('norm'),
                    'unit' => $group->first()['unit'],
                    'sort' => $group->first()['sort'],
                ];
            })->sortBy('material')->sortBy('sort');
        }

        return $records;
    }

    private function node($materials,$designation_id)
    {
        $specifications = Specification
            ::where('designation_id', $designation_id)
            ->with(['designations', 'designationEntry', 'designationMaterial.material.unit'])
            ->get();

        if ($specifications->isNotEmpty()) {

            foreach ($specifications as $specification) {

                if (str_starts_with($specification->designationEntry->designation, 'КР') || str_starts_with($specification->designationEntry->designation, 'ПИ0')) {

                    $type = str_starts_with($specification->designationEntry->designation, 'КР') ? 'kr' : 'pki';

                    if($this->type_report == 0) {
                        $materials->push((object)[
                            'material' => $specification->designationEntry->designation,
                            'norm' => $specification->quantity,
                            'unit' => $type == 'kr' ? 'шт' : $specification->designationEntry->unit->unit ?? "",
                            'sort' => $type == 'kr' ? 1 : 2
                        ]);
                    }elseif($this->type_report == 1) {
                        $this->all_materials[] = array(
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
                            'material_id' => $material->material->id,
                            'material' => $material->material->name,
                            'norm' => $material->norm,
                            'unit' => $material->material->unit->unit ?? "",
                            'code_1c' => $material->material->code_1c,
                            'sort' => 0);
                    }elseif($this->type_report == 0) {
                        $materials->push((object)[
                            'material' => $material->material->name,
                            'norm' => $material->norm,
                            'unit' => $material->material->unit->unit ?? "",
                            'sort' => 0
                        ]);
                    }
                }
                $this->node($materials,$specification->designation_entry_id);

            }
        }
        return $materials;
    }
}
