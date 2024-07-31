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
}
