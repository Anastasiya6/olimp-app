<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\SpecificationNormService;

class SpecificationNormController extends Controller
{
    public function specificationNorm($order_number,$department,SpecificationNormService $service)
    {
        $service->specificationNorm($order_number,$department);
    }
}
