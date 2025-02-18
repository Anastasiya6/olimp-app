<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\DetailSpecificationNormService;

class DetailSpecificationNormController extends Controller
{
    public function detailSpecificationNorm($order_name_id,$department,DetailSpecificationNormService $service)
    {
        $service->detailSpecificationNorm($order_name_id,$department);
    }
}
