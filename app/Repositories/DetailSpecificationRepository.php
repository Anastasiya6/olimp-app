<?php

namespace App\Repositories;

use App\Models\GroupMaterial;
use App\Models\ReportApplicationStatement;
use App\Repositories\Interfaces\DetailSpecificationRepositoryInterface;
use Illuminate\Support\Str;

class DetailSpecificationRepository implements DetailSpecificationRepositoryInterface
{
    public function getByOrderDepartment($order_name_id,$department){
        if($department != 0){
            return ReportApplicationStatement
                ::where('order_name_id',$order_name_id)
                ->whereRaw("SUBSTR(tm, 1, 2) = '$department'")
                /*->whereHas('designation', function ($query) use ($department){
                $query-> whereRaw("SUBSTRING(route, 1, 2) = '$department'");
            })*/
                ->has('designationMaterial.material')
                ->with('designationEntry','designationMaterial.material')
                ->orderBy('order_designationEntry_letters')
                ->orderBy('order_designationEntry')
                ->get();
        }else {
            return ReportApplicationStatement
                ::where('order_name_id', $order_name_id)
                /*->whereHas('designation', function ($query) use ($department){
                $query-> whereRaw("SUBSTRING(route, 1, 2) = '$department'");
            })*/
                ->has('designationMaterial.material')
                ->with('designationEntry', 'designationMaterial.material')
                ->orderBy('order_designationEntry_letters')
                ->orderBy('order_designationEntry')
                ->get();
        }
    }

    public function getByOrderDepartments($order_name_id,$sender_department,$receiver_department){

        return ReportApplicationStatement
            ::where('order_name_id',$order_name_id)
            ->whereRaw("SUBSTR(tm, 1, 2) = '$sender_department'")
            ->when($receiver_department != 0, function ($query) use ($receiver_department) {
                return $query->whereRaw("SUBSTR(tm, -2) = '$receiver_department'");
            })
            ->has('designationMaterial.material')
            ->with('designationEntry','designationMaterial.material')
            ->orderBy('order_designationEntry_letters')
            ->orderBy('order_designationEntry')
            ->get();

    }


    public function getByOrderDepartmentPkiKr($order_name_id,$department)
    {
        if($department != 0) {
            return ReportApplicationStatement
                ::whereHas('designationEntry', function ($query) {
                    $query->where('designation', 'like', 'КР%')
                        ->orWhere('type', 1);
                })
                ->where('order_name_id', $order_name_id)
                ->whereRaw("SUBSTR(tm, -2) = ?", [$department])
                ->with('designation','designationEntry')
                ->get();
        }else{
            return ReportApplicationStatement
                ::whereHas('designationEntry', function ($query) {
                    $query->where('designation', 'like', 'КР%')
                        ->orWhere('type', 1);
                })
                ->where('order_name_id', $order_name_id)
                ->with('designation','designationEntry')
                ->get();
        }
    }

    public function getByOrderDepartmentPkiKrItems($order_name_id,$department): \Illuminate\Support\Collection
    {

        $items = $this->getByOrderDepartmentPkiKr($order_name_id,$department);

        $kr = $this->getKr($items);

        $pki = $this->getPki($items);

        $itemsCollection = collect($pki);

        return $itemsCollection->merge($kr);

    }

    public function getPki($items){

        $pki_items = $items->filter(function ($item) {
            return $item->designationEntry->type == 1;
        });

        return $this->generateData($pki_items);
    }

    public function getKR($items){

        $kr_items = $items->filter(function ($item) {
            return Str::startsWith($item->designationEntry->designation, 'КР');
        });

        return $this->generateData($kr_items);
    }

    public function generateData($items){

        $data = $items->map(function ($item) {
            return [
                'id' => $item->designationEntry->id,
                'material_name' => $item->designationEntry->name,
                'detail_name' => $item->designation->designation,
                'unit' => $item->designationEntry->unit->unit??'шт',
                'norm' => $item->quantity,
                'quantity_total' => $item->quantity > 0 ? $item->quantity_total / $item->quantity : '',
            ];
        });

        $groupedData = $this->groupData($data);

        return $this->addGroupMaterial($groupedData,0);
    }

    public function groupData($items){

        return $items->groupBy('id')->map(function ($group) {
            // Группируем внутри каждой группы по названию материала
            //return $group->groupBy('material_name')->map(function ($materialDetails, $materialName) {
            // Группируем далее по наименованию детали
            return $group->groupBy('detail_name')->map(function ($details) {
                // Возвращаем детали для каждой детали в группе
                return [
                    'id' => $details->first()['id'],
                    'material_name' => $details->first()['material_name'],
                    'detail_name' => $details->first()['detail_name'],
                    'quantity_total' => $details->sum('quantity_total'),
                    'unit' => $details->first()['unit'],
                    'norm' => $details->first()['norm'],
                ];
            });
        });
    }

    public function addGroupMaterial($groupedData,$isGroup=1){

        $new_array = array();

        foreach($groupedData as $material_id=>$groupedData_material){
            if($isGroup){
                $group_materials = GroupMaterial::where('material_id',$material_id)->get();
                if($group_materials->count() > 0) {

                    $group_materials->load('materialEntry');

                    foreach ($group_materials as $group) {

                        foreach($groupedData_material as $detail){
                            $detail['norm'] = $detail['norm']*$group->norm;
                            $detail['unit'] = $group->materialEntry->unit->unit;
                            $new_array[$group->materialEntry->name.$group->material_entry_id."_Group"][$group->materialEntry->name][] = $detail;
                        }
                        usort($new_array[$group->materialEntry->name . $group->material_entry_id . "_Group"][$group->materialEntry->name], function ($a, $b) {
                            return strcmp($a['detail_name'], $b['detail_name']);
                        });
                    }

                }else{
                    $new_array = $this->notInGroup($groupedData_material, $material_id,$new_array);
                }
            }else{
                $new_array = $this->notInGroup($groupedData_material, $material_id,$new_array);
            }

        }
        ksort($new_array);

        return $new_array;
    }

    public function notInGroup($groupedData_material,$material_id,$new_array){
        foreach($groupedData_material as $detail){
            $new_array[$detail['material_name'].$material_id][$detail['material_name']][] = $detail;
        }
        usort($new_array[$detail['material_name'].$material_id][$detail['material_name']], function ($a, $b) {
            return strcmp($a['detail_name'], $b['detail_name']);
        });
        return $new_array;
    }
}
