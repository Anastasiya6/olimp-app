<?php

namespace App\Repositories;

use App\Models\ReportApplicationStatement;
use App\Repositories\Interfaces\ReportApplicationStatementRepositoryInterface;

class ReportApplicationStatementRepository implements ReportApplicationStatementRepositoryInterface
{
    public function getByOrder($order_name_id)
    {
        return ReportApplicationStatement
            ::where('order_name_id',$order_name_id)
            ->has('designationMaterial.material')
            ->with('designationEntry','designationMaterial.material')
            ->get();
    }

    public function getDataByDepartment($items,$department_number)
    {
        if ($department_number != 0){
            $data = $items->flatMap(function ($item) use ($department_number){
                return $item->designationMaterial->filter(function ($designationMaterial) use ($item,$department_number) {
                    // Проверка на соответствие department
                    $department = substr($item->designationEntry->route, 0, 2);
                    return $department == $department_number;
                })->map(function ($designationMaterial) use ($item) {
                    return [
                        'id' => $designationMaterial->material->id,
                        'name' => $designationMaterial->material->name,
                        'unit' => $designationMaterial->material->unit->unit,
                        'code_1c' => $designationMaterial->material->code_1c,
                        'norm' => $designationMaterial->norm * $item->quantity_total,
                        'department' => substr($item->designationEntry->route, 0, 2),
                    ];
                });
            })->sortBy('id');

        }else {

            $data = $items->flatMap(function ($item) {
                return $item->designationMaterial->map(function ($designationMaterial) use ($item) {
                    return [
                        'id' => $designationMaterial->material->id,
                        'name' => $designationMaterial->material->name,
                        'unit' => $designationMaterial->material->unit->unit,
                        'code_1c' => $designationMaterial->material->code_1c,
                        'norm' => $designationMaterial->norm * $item->quantity_total,
                        'department' => substr($item->designationEntry->route, 0, 2),
                    ];
                });
            })->sortBy('id');
        }

        return $data->groupBy('id')->flatMap(function ($items) {
            return $items->groupBy('department')->map(function ($departmentItems) {
                return [
                    'id' => $departmentItems->first()['id'], // Берем ID из первого элемента группы
                    'name' => $departmentItems->first()['name'], // Берем название материала из первого элемента группы
                    'unit' => $departmentItems->first()['unit'], // Берем единицу измерения из первого элемента группы
                    'department' => $departmentItems->first()['department'], // Берем цех из первого элемента группы
                    'code_1c' => $departmentItems->first()['code_1c'],
                    'norm' => round($departmentItems->sum('norm'),3), // Суммируем количество по всем элементам группы
                    'norm_with_koef' => round($departmentItems->sum('norm') * ((str_starts_with($departmentItems->first()['name'], 'Лист') || str_starts_with($departmentItems->first()['name'], 'Плита')) ? 1.2 : 1.1),3),
                    'sort' => 0
                ];
            })->values(); // Преобразуем коллекцию в массив значений
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

    public function getByOrderPki($order_name_id)
    {
        return ReportApplicationStatement
            ::whereHas('designationEntry', function ($query) {
                $query->where('type', 1);
            })
            ->where('order_name_id',$order_name_id)
            ->with('designationEntry')
            ->get();
    }

    public function getByOrderKr($order_name_id)
    {
        return ReportApplicationStatement
            ::whereHas('designationEntry', function ($query) {
                $query->where('designation', 'like', 'КР%');
            })
            ->where('order_name_id',$order_name_id)
            ->with('designationEntry')
            ->get();
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
        if ($department != 0){
            $pki_data = $pki_data->filter(function ($item) use($department){
                return $item['department'] == $department;
            });
        }

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
}
