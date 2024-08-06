<?php

namespace App\Repositories\Interfaces;

interface ReportApplicationStatementRepositoryInterface
{
    public function getByOrder($order_number);

    public function getByOrderPki($order_number);

    public function getByOrderKr($order_number);

    public function getDataByDepartment($items,$department_number);

    public function getDataKrByDepartment($kr_items,$department);

    public function getDataPkiByDepartment($pki_items,$department);

}
