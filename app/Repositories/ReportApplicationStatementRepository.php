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
}
