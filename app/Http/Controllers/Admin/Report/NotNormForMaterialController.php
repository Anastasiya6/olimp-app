<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\NotNormForMaterialService;

class NotNormForMaterialController extends Controller
{
    public function notNormForMaterial(NotNormForMaterialService $service)
    {
        $service->notNormForMaterial();
    }
}
