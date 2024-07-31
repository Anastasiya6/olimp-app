<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\PlanTaskSpecificationNormService;

class PlanTaskSpecificationNormController extends Controller
{
    public function planTaskSpecificationNorm($order_name_id,$department,PlanTaskSpecificationNormService $service)
    {
        $service->specificationNorm($order_name_id,$department);
    }
}
