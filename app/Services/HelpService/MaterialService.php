<?php

namespace App\Services\HelpService;

use App\Models\MaterialPurchase;
use App\Models\Purchase;
use App\Models\Specification;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;

class MaterialService
{
    public array $all_materials;

    public int $type_report;

    public $type_group = null;

    public int $sender_department_number;

    public int $sender_department_id;

    private DepartmentRepositoryInterface $departmentRepository;

    public function __construct(DepartmentRepositoryInterface $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;

    }

    public function material($records,$type_report,$sender_department_id,$type_group = null)
    {
        $this->sender_department_number = $this->departmentRepository->getByDepartmentIdFirst($sender_department_id)?->number;

        $this->sender_department_id = $sender_department_id;

        $this->type_report = $type_report;

        $this->all_materials = [];

        $this->type_group = $type_group;

        foreach($records as $item){

            $item->materials = collect();

            $array_materials = $this->checkMaterial($item->designationMaterial,$item->designation_id,$item->designation_id,$item->with_purchased,1,$item->with_material_purchased);

            $this->fillMaterials($item->materials,$item->designation->designation,1,$array_materials,$item->quantity);

            $this->node($item->materials,$item->designation_id,$item->quantity,$item->with_purchased,$item->with_material_purchased,$item->quantity);

        }

        $records->materials = $this->all_materials;

        return $this->sortByGroup($records);
    }

    public function getTypeMaterial($type,$material=''): array
    {

        if($type == 'detail' || $type == 'purchase' ) {

            return ["", 1];

        }else{
            if(str_starts_with($material, 'Лист') || str_starts_with($material, 'Плита')) {

                return ["* 1.2",1.2];
            }

            return ["* 1.1",1.1];

        }
    }

    private function checkMaterial($designationMaterial,$designation_id, $designation_entry_id, $with_purchased,$quantity,$with_material_purchased)
    {
        if($with_purchased == 1) {

            $purchase = $this->getPurchase($designation_id, $designation_entry_id, $quantity);

            if(!empty($purchase)){

                return $purchase;
            }
        }
        if($with_material_purchased == 1) {

            $material_purchase = $this->getMaterialPurchase($designation_id, $designation_entry_id,$quantity);

            if(!empty($material_purchase)){

                return $material_purchase;
            }
        }


        return $this->getMaterial($designationMaterial,$quantity);

    }

    private function getMaterial($designationMaterial,$quantity)
    {
        $array = array();
        if($designationMaterial->isNotEmpty()) {
            foreach ($designationMaterial as $material) {
                if($this->sender_department_id != $material->department_id ){
                    $material->load('designation');
                    $array[] = [
                        'type' => 'detail',
                        'material_id' => $material->designation->id,
                        'material' => $material->designation->name,
                        'norm' => 1,
                        'quantity' => $quantity,
                        'unit' => "",
                        'code_1c' => ""
                    ];
                }else {
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
                'code_1c' => $purchase->code_1c
            ]);
        }

        return array();
    }
    private function getMaterialPurchase($designation_id, $designation_entry_id,$quantity)
    {
        $material_purchase = MaterialPurchase::where('designation_id', $designation_id)->where('designation_entry_id', $designation_entry_id)->first();

        if ($material_purchase) {

            return array([
                'type' => 'material_purchase',
                'material_id' => $material_purchase->material_id,
                'material' => $material_purchase->material->name,
                'norm' => $material_purchase->norm,
                'quantity' => $quantity,
                'unit' => $material_purchase->material->unit->unit,
                'code_1c' => $material_purchase->code_1c
            ]);
        }

        return array();
    }
    private function node($materials,$designation_id,$quantity_node,$with_purchased,$with_material_purchased,$order_quantity=1,$pred_quantity_node=1)
    {
        $specifications = Specification
            ::where('designation_id', $designation_id)
            ->join('designations', 'designations.id', '=', 'designation_entry_id')
            ->with(['designations', 'designationEntry', 'designationMaterial.material.unit'])
            ->get();

        if ($specifications->isNotEmpty()) {

            foreach ($specifications as $specification) {

                $tm = StatementService::getTm($specification);

                $route = SpecificationService::getRoute($specification,$tm,$this->sender_department_number);

                if($route == 0 || $route == $this->sender_department_number) {
                    if (str_starts_with($specification->designationEntry->designation, 'КР') || str_starts_with($specification->designationEntry->designation, 'ПИ0')) {

                        $type = str_starts_with($specification->designationEntry->designation, 'КР') ? 'kr' : 'pki';

                        if ($this->type_report == 0) {
                            $materials[] = array(
                                'type' => $type,
                                'detail' => $specification->designationEntry->designation,
                                'material' => $specification->designationEntry->name,
                                'material_id' => '',
                                'norm' => $specification->quantity,
                                'pred_quantity_node' => $quantity_node,
                                'quantity_node' => $quantity_node,
                                'order_quantity' => $order_quantity,
                                'print_number' => $specification->quantity . ' * ' . $quantity_node,
                                'print_value' => $specification->quantity * $quantity_node,
                                'unit' => $type == 'kr' ? 'шт' : $specification->designationEntry->unit->unit ?? "",
                                'code_1c' =>   $specification->designationEntry->code_1c,
                                'sort' => $type == 'kr' || 'pki' ? 1 : 2);
                            /*$materials->push((object)[
                                'type' => $type,
                                'detail' => $specification->designationEntry->designation,
                                'material' => $specification->designationEntry->name,
                                'norm' => $specification->quantity,
                                'pred_quantity_node' => $quantity_node,
                                'code_1c' => $specification->designationEntry->code_1c,
                                'unit' => $type == 'kr' ? 'шт' : $specification->designationEntry->unit->unit ?? "",
                                'sort' => $type == 'kr' ? 1 : 2*/
                            //]);
                        } elseif ($this->type_report == 1) {

                            $this->all_materials[] = array(
                                'type' => $type,
                                'detail' => $this->type_group == 'detail' ? $specification->designations->designation : $specification->designationEntry->designation,
                                'material' => $specification->designationEntry->name,
                                'material_id' => $this->type_group == 'detail' ? $specification->designationEntry->name : $specification->designationEntry->id . $type,
                                'norm' => $specification->quantity,
                                'pred_quantity_node' => $pred_quantity_node,
                                'quantity_node' => $quantity_node,
                                'order_quantity' => $order_quantity,
                                'print_number' => $specification->quantity . ' * ' . $quantity_node,
                                'print_value' => $specification->quantity * $quantity_node,
                                'unit' => $type == 'kr' ? 'шт' : $specification->designationEntry->unit->unit ?? "",
                                'code_1c' =>  $specification->designationEntry->code_1c,
                                'sort' => $type == 'kr' || 'pki' ? 1 : 2);
                            /*if($this->type_group == 'detail'){

                                $this->all_materials[] = array(
                                    'type' => $type,
                                    'detail' => $specification->designations->designation,
                                    'material' =>$specification->designationEntry->designation,
                                    'material_id' => $specification->designationEntry->name,
                                    'norm' => $specification->quantity * $quantity,
                                    'pred_quantity_node' => $quantity,
                                    'quantity_norm' => $specification->quantity * $quantity,
                                    'unit' =>   $type == 'kr' ? 'шт' : $specification->designationEntry->unit->unit ?? "",
                                    'code_1c' => $specification->designationEntry->code_1c,
                                    'sort' => $type == 'kr' || 'pki' ? 1 : 2);
                            }else{

                                $this->all_materials[] = array(
                                    'type' => $type,
                                    'detail' => $specification->designationEntry->designation,
                                    'material_id' => $specification->designationEntry->id . $type,
                                    'material' => $specification->designationEntry->name,
                                    'norm' => $specification->quantity * $quantity,
                                    'pred_quantity_node' => $quantity,
                                    'quantity_norm_quantity_detail' => $specification->quantity * $quantity,
                                    'code_1c' => $specification->designationEntry->code_1c,
                                    'unit' => $type == 'kr' ? 'шт' : $specification->designationEntry->unit->unit ?? "",
                                    'sort' => $type == 'kr' ? 1 : 2);*/
                            }
                       // }
                    }
                }

                $array_material = $this->checkMaterial($specification->designationMaterial,$specification->designation_id,$specification->designation_entry_id,$with_purchased,$specification->quantity,$with_material_purchased);

                $this->fillMaterials($materials,$specification->designationEntry->designation,$quantity_node,$array_material,$order_quantity,$pred_quantity_node);

                $this->node($materials,$specification->designationEntry->id,$specification->quantity,$with_purchased,$with_material_purchased,$order_quantity,$quantity_node);

            }
        }
        return $materials;
    }

    private function fillMaterials($materials,$designation,$quantity_node,$array_materials,$order_quantity,$pred_quantity_node=1)
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
                    'pred_quantity_node' => $pred_quantity_node,
                    'quantity_node' => $array_material['quantity'],
                    'print_number' => $array_material['quantity'] . ' * ' . $quantity_node . ' * ' . $array_material['norm'] . ' * ' . $pred_quantity_node,
                    'print_value' => $array_material['quantity'] * $quantity_node * $array_material['norm'] * $pred_quantity_node,
                    'order_quantity' => $order_quantity,
                    'unit' => $array_material['unit'] ?? "",
                    'code_1c' => $array_material['code_1c'],
                    'sort' => 0);

               // 'quantity_norm' => $array_material['quantity'] * $quantity_node * $array_material['norm'] * $pred_quantity_node,
              // 'quantity_norm_quantity_detail' => $array_material['quantity'] * $quantity_node * $array_material['norm'],

         //   if ($this->type_report == 1) {

                /*$this->all_materials[] = array(
                    'type' => $array_material['type'],
                    'detail' => $designation,
                    'material' => $array_material['material'],
                    'material_id' => $array_material['material_id'],
                    'norm' => $array_material['norm'],
                    'pred_quantity_node' => $pred_quantity_node,
                    'quantity_norm' => $array_material['quantity'] * $quantity * $array_material['norm'] * $pred_quantity_node,
                    'quantity_norm_quantity_detail' => $array_material['quantity'] * $quantity * $array_material['norm'],
                    'unit' => $array_material['unit'] ?? "",
                    'code_1c' => $array_material['code_1c'],
                    'sort' => 0);*/
            } elseif ($this->type_report == 0) {

                $materials[] = array(
                    'type' => $array_material['type'],
                    'detail' => $designation,
                    'material' => $array_material['material'],
                    'material_id' => $array_material['material_id'],
                    'norm' => $array_material['norm'],
                    'pred_quantity_node' => $pred_quantity_node,
                    'quantity_node' => $array_material['quantity'],
                    'print_number' => $array_material['quantity'] . ' * ' . $quantity_node. ' * ' . $array_material['norm'] . ' * ' . $pred_quantity_node,
                    'print_value' => $array_material['quantity'] * $quantity_node * $array_material['norm'] * $pred_quantity_node,
                    'order_quantity' => $order_quantity,
                    'unit' => $array_material['unit'] ?? "",
                    'code_1c' => $array_material['code_1c'],
                    'sort' => 0
                );
           }
        }

        return $materials;
    }

    private function sortByGroup($records)
    {
       // dd($records);
        if($this->type_report == 1) {

            $records = collect($records->materials);
          // dd($records);
            if($this->type_group == 'material_id'){
                //for plan-task-specification-norm
                return $records->groupBy('material_id')->map(function ($group) {
                    return [
                            'type' => $group->first()['type'],
                            'detail' => $group->first()['detail'],
                            'material' => $group->first()['material'],
                            'material_id' => $group->first()['material_id'],
                            'norm' => $group->sum('norm'),
                            'pred_quantity_node' => $group->sum('pred_quantity_node'),
                            'quantity_node' => $group->sum('quantity_node'),
                            'order_quantity' => $group->first()['order_quantity'],
                            'print_number' => $group->sum('print_value'),
                            'print_value' => round($group->sum('print_value'),3),
                            'unit' => $group->first()['unit'],
                            'code_1c' => $group->first()['code_1c'],
                            'sort' => $group->first()['sort'],


                       /* 'type' => $group->first()['type'],
                        'material_id' => $group->first()['material_id'],
                        'code_1c' => $group->first()['code_1c'],
                        'material' => $group->first()['material'],
                        'detail' => $group->first()['detail'],
                        'norm' => $group->sum('norm'),
                        'pred_quantity_node' => $group->sum('pred_quantity_node'),
                        'quantity_norm' => $group->sum('quantity_norm'),
                        'quantity_norm_quantity_detail' => $group->sum('quantity_norm_quantity_detail'),
                        'unit' => $group->first()['unit'],
                        'sort' => $group->first()['sort'],*/
                    ];
                })->sortBy('material')->sortBy('sort');

            }elseif($this->type_group == 'detail'){
                   // dd($records);
                $details = $this->getSortItems($records->filter(fn($item) => $item['sort'] == 0)->values());

                $pki = $this->getSortItems($records->filter(fn($item) => $item['sort'] == 1)->values());

                return $details->merge($pki);;
            }
        }
        return $records;
    }

    public function getSortItems($items)
    {
        //for plan-task-detail-specification-norm
        return $items->groupBy('material')->map(function ($group) {
            // Группируем внутри каждой группы по названию материала
            //return $group->groupBy('material_name')->map(function ($materialDetails, $materialName) {
            // Группируем далее по наименованию детали

            return $group->groupBy('detail')->map(function ($details) {
                return [
                    'id' => $details->first()['material_id'],
                    'type' => $details->first()['material_id'],
                    'detail' => $details->first()['detail'],
                    'material' => $details->first()['material'],
                    'material_id' => $details->first()['material_id'],
                    'norm' => $details->first()['norm'],
                    'pred_quantity_node' => $details->first()['pred_quantity_node'],
                    'quantity_node' => $details->first()['quantity_node'],
                    'order_quantity' => $details->first()['order_quantity'],
                    'print_number' =>  $details->sum('print_value'),
                    'print_value' =>  round($details->sum('print_value'),3),
                    'unit' => $details->first()['unit'],
                    'code_1c' => $details->first()['code_1c'],
                    'sort' => $details->first()['sort'],

                /*    'id' => $details->first()['material_id'],
                    'type' => $details->first()['type'],
                    'material' => $details->first()['material'],
                    'detail' => $details->first()['detail'],
                    'pred_quantity_node' => $details->first()['pred_quantity_node'],
                    'quantity_norm' => $details->sum('quantity_norm'),
                    'unit' => $details->first()['unit'],
                    'norm' => $details->first()['norm'],
                    'sort' => $details->first()['sort'],*/
                ];
            })->sortBy('detail'); // Затем сортируем по sort (0 в начале, 1 в конце)
        })->sortKeys();
    }

    private function getRoute($specification,$tm)
    {
        if (str_starts_with($specification->designationEntry->designation, 'КР') || $specification->designationEntry->type == 1) {
            // Строка начинается с 'КР' или это ПИ0

            return $this->sender_department_number == 0 ? 0 : substr($tm, -2);

        }else {

            return $this->sender_department_number == 0 ? 0 : substr($specification->designationEntry->route, 0, 2);
        }
    }

}
