<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Services\Reports\EntryDetailDesignationService;

class EntryDetailDesignationController extends Controller
{
    public function entryDetailDesignation($designation_number,$department,EntryDetailDesignationService $service)
    {
        if($designation_number){

            $designation = Designation::where('designation', $designation_number)->first();

            if($designation){

                $service->entryDetailDesignation($designation,$department);
            }
        }
    }
}
