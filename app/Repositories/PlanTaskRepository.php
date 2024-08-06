<?php

namespace App\Repositories;

use App\Models\PlanTask;
use App\Repositories\Interfaces\PlanTaskRepositoryInterface;

class PlanTaskRepository implements PlanTaskRepositoryInterface
{
    public function getByOrderPki($order_name_id)
    {
        return PlanTask
            ::whereHas('designationEntry', function ($query) {
                $query->where('type', 1);
            })
            ->where('order_name_id',$order_name_id)
            ->with('designationEntry')
            ->get();
    }

    public function getByOrderDepartment($order_name_id,$sender_department_id)
    {
        return PlanTask
            ::where('order_name_id',$order_name_id)
            ->where('sender_department_id',$sender_department_id)
            ->has('designationMaterial.material')
            ->with('senderDepartment','designationEntry','designationMaterial.material')
            ->get();
    }

    public function getByOrderKr($order_name_id)
    {
        return PlanTask
            ::whereHas('designationEntry', function ($query) {
                $query->where('designation', 'like', 'КР%');
            })
            ->where('order_name_id',$order_name_id)
            ->with('designationEntry')
            ->get();
    }
    public function getDataByDepartment($items)
    {
        $data = $items->flatMap(function ($item) {
            return $item->designationMaterial->map(function ($designationMaterial) use ($item) {
                return [
                    'id' => $designationMaterial->material->id,
                    'name' => $designationMaterial->material->name,
                    'unit' => $designationMaterial->material->unit->unit,
                    'code_1c' => $designationMaterial->material->code_1c,
                    'norm' => $designationMaterial->norm * $item->quantity_total,
                    'department' => $item->senderDepartment->number,
                ];
            });
        })->sortBy('id');

        return $data->groupBy('id')->flatMap(function ($items) {
            return $items->groupBy('department')->map(function ($departmentItems) {
                return [
                    'id' => $departmentItems->first()['id'], // Берем ID из первого элемента группы
                    'name' => $departmentItems->first()['name'], // Берем название материала из первого элемента группы
                    'unit' => $departmentItems->first()['unit'], // Берем единицу измерения из первого элемента группы
                    'department' => $departmentItems->first()['department'], // Берем цех из первого элемента группы
                    'code_1c' => $departmentItems->first()['code_1c'],
                    'norm' => $departmentItems->sum('norm'), // Суммируем количество по всем элементам группы
                    'norm_with_koef' => $departmentItems->sum('norm') * 1.2,
                    'sort' => 0
                ];
            })->values(); // Преобразуем коллекцию в массив значений
        });
    }

    public function getDataPkiByDepartment($pki_items,$department)
    {
        $pki_data = $pki_items->map(function ($item) {
            return [
                'id' => $item->designationEntry->id.'pki',
                'name' => $item->designationEntry->name,
                'unit' => $item->designationEntry->unit->unit??'шт',
                'norm' =>$item->quantity_total,
                'department' => substr($item->tm, -2),
            ];
        });

        $pki_data = $pki_data->filter(function ($item) use($department){
            return $item['department'] == $department;
        });

        return $pki_data->groupBy('id')->flatMap(function ($items) {
            return $items->groupBy('department')->map(function ($departmentItems) {
                return [
                    'id' => $departmentItems->first()['id'], // Берем ID из первого элемента группыента группы
                    'department' => $departmentItems->first()['department'], // Берем цех из первого элемен
                    'name' => $departmentItems->first()['name'], // Берем название материала из первого элемента группы
                    'unit' => $departmentItems->first()['unit'], // Берем единицу измерения из первого элемта группы
                    'norm' => $departmentItems->sum('norm'), // Суммируем количество по всем элементам группы
                    'norm_with_koef' => $departmentItems->sum('norm'),
                    'sort' => 2
                ];
            })->values();
        });
    }

    public function getDataKrByDepartment($kr_items,$department)
    {
        $kr_data = $kr_items->map(function ($item) {
            return [
                'id' => $item->designationEntry->id.'kr',
                'name' => $item->designationEntry->name,
                'unit' => 'шт',
                'norm' =>$item->quantity_total,
                'department' => substr($item->tm, -2),
            ];
        });

        if ($department != 0){
            $kr_data = $kr_data->filter(function ($item) use($department){
                return $item['department'] == $department;
            });
        }

        return $kr_data->groupBy('id')->flatMap(function ($items) {
            return $items->groupBy('department')->map(function ($departmentItems) {

                return [
                    'name' => $departmentItems->first()['name'], // Берем название материала из первого элемента группы
                    'unit' => $departmentItems->first()['unit'], // Берем единицу измерения из первого элемента группы
                    'department' => $departmentItems->first()['department'], // Берем цех из первого элемента группы
                    'norm' => $departmentItems->sum('norm'), // Суммируем количество по всем элементам группы
                    'norm_with_koef' => $departmentItems->sum('norm'),
                    'sort' => 1
                ];
            })->values();
        });
    }
}
