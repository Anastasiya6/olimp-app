<?php

namespace App\Repositories\Interfaces;

interface DetailSpecificationRepositoryInterface
{
    public function getByOrderDepartmentPkiKr($order_name_id,$department);

    public function getByOrderDepartmentPkiKrItems($order_name_id,$department);

}
