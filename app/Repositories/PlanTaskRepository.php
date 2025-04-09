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
            ->with('designation','designationMaterial.material')
            ->get();
    }

    public function getByOrderDepartments($order_name_id,$sender_department_id,$receiver_department_id)
    {
        return PlanTask
            ::where('order_name_id',$order_name_id)
            ->where('sender_department_id',$sender_department_id)
            ->when($receiver_department_id!= 0, function ($query) use ($receiver_department_id) {
                return $query->where('receiver_department_id', $receiver_department_id);
            })
           // ->where('receiver_department_id',$receiver_department_id)
            ->with('designation','designationMaterial.material')
            ->get();
    }
}
