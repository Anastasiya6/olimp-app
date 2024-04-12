<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\NotNormForMaterialService;

class NotNormForMaterialController extends Controller
{
    public function notNormForMaterial($department,NotNormForMaterialService $service)
    {
        $service->notNormForMaterial($department);
    }
}
