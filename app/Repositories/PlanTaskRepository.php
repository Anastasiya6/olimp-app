<?php

namespace App\Repositories;

use App\Models\PlanTask;
use App\Repositories\Interfaces\PlanTaskRepositoryInterface;

class PlanTaskRepository implements PlanTaskRepositoryInterface
{
    public function getByOrderDepartment($order_name_id,$sender_department_id)
    {
        return PlanTask
            ::where('order_name_id',$order_name_id)
            ->where('sender_department_id',$sender_department_id)
            ->has('designationMaterial.material')
            ->with('senderDepartment','designationEntry','designationMaterial.material')
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
}
