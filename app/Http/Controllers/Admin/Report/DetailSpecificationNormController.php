<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\DetailSpecificationNormService;

class DetailSpecificationNormController extends Controller
{
    public function detailSpecificationNorm($department,$order_number,DetailSpecificationNormService $service)
    {
        $service->detailSpecificationNorm($department,$order_number);
    }
}
