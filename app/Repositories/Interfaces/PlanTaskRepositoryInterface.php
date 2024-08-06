<?php

namespace App\Repositories\Interfaces;

interface PlanTaskRepositoryInterface
{
    public function getByOrderDepartment($order_name_id,$sender_department_id);

}
