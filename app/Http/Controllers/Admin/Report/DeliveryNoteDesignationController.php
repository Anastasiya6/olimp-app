<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\DeliveryNoteDesignationService;

class DeliveryNoteDesignationController extends Controller
{
    public function deliveryNoteDesignation($designation,DeliveryNoteDesignationService $service)
    {
        $service->deliveryNoteDesignation($designation);
    }
}
