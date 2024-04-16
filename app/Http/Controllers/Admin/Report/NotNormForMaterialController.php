<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\NotNormForMaterialService;

class NotNormForMaterialController extends Controller
{
    public function notNormForMaterial($department,$order_number,NotNormForMaterialService $service)
    {
        $service->notNormForMaterial($department,$order_number);
    }
}
