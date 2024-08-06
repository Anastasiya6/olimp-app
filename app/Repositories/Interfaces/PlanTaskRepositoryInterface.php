<?php

namespace App\Repositories\Interfaces;

interface PlanTaskRepositoryInterface
{
    public function getByOrderDepartment($order_name_id,$sender_department_id);

    public function getByOrderPki($order_number);

    public function getByOrderKr($order_number);

    public function getDataByDepartment($items);

    public function getDataPkiByDepartment($pki_items,$department);

    public function getDataKrByDepartment($kr_items,$department);

}
