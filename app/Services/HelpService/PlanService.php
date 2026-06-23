<?php

namespace App\Services\HelpService;

use App\Models\PlanTask;
use App\Models\ReportApplicationStatement;
use App\Models\Specification;

class PlanService
{
    public static function getDetailFromPlan($designation_id,$order_name_id)
    {
        $designationId = $designation_id;
        $orderId = $order_name_id;

        $checked = [];

        while ($designationId && !in_array($designationId, $checked)) {

            $checked[] = $designationId;

            $planTask = PlanTask::where('designation_id', $designationId)
                ->where('order_name_id', $orderId)
                ->first();

            if ($planTask) {
               // dd($planTask);
                return $planTask;
                //$saved = true;
                //break;
            }

            $report = ReportApplicationStatement::where('designation_entry_id', $designationId)
                ->where('order_name_id', $orderId)
                ->first();

            if (!$report) {
                break;
            }

            $designationId = $report->designation_id;
        }


        $designationId = $designation_id;

        $checked = [];

        while ($designationId && !in_array($designationId, $checked)){

            $checked[] = $designationId;

            $planTask = PlanTask::where('designation_id', $designationId)
                ->where('order_name_id', $orderId)
                ->first();

            if ($planTask) {
                dd($planTask);
                return $planTask;
                //break;
            }

            $specification = Specification::where('designation_entry_id', $designationId)
                ->first();
            if (!$specification) {
                break;
            }

            $designationId = $specification->designation_id;
        }

    }
}
