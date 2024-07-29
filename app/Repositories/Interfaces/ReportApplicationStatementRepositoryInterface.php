<?php

namespace App\Repositories\Interfaces;

interface ReportApplicationStatementRepositoryInterface
{
    public function getByOrder($order_number);

    public function getByOrderPki($order_number);

    public function getByOrderKr($order_number);
}
