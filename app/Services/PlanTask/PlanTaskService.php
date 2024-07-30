<?php

namespace App\Services\PlanTask;
use App\Models\Designation;
use App\Models\PlanTask;
use App\Services\HelpService\HelpService;
use Illuminate\Http\Request;

class PlanTaskService
{
    public function store(Request $request)
    {

        $designation = Designation::where('designation', $request->designation_entry_designation)->first();

        if(isset($designation->id)){

            PlanTask::create(
                [
                    'order_name_id' => $request->order_name_id,
                    'designation_entry_id' => $designation->id,
                    'order_designationEntry' => HelpService::getNumbers($request->designation_entry_designation),
                    'order_designationEntry_letters' => HelpService::getLetters($request->designation_entry_designation),
                    'quantity' => $request->quantity,
                    'quantity_total' => $request->quantity,
                    'category_code' => $request->category_code,
                    'type' => $request->type,
                    'tm' => $request->tm
                ]
            );
        }
    }
}
