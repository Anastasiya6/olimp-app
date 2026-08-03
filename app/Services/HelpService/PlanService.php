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
                return collect([$planTask]);
            }

            $reports = ReportApplicationStatement::where('designation_entry_id', $designationId)
                ->where('order_name_id', $orderId)
                ->get();

            if ($reports->isEmpty()) {
                $reports = Specification::where('designation_entry_id', $designationId)
                    ->get();
            }

            if ($reports->isEmpty()) {
                break;
            }

            $planTasks = collect();

            foreach ($reports as $report) {

                $task = self::getFromSpecification($report->designation_id,$orderId);
                if ($task) {
                    $planTasks->push($task);
                }
            }

            if ($planTasks->isNotEmpty()) {

                return $planTasks;
            }
            break;
        }

    }

    public static function getFromSpecification($designation_id,$orderId){

        $designationId = $designation_id;

        $checked = [];

        while ($designationId && !in_array($designationId, $checked)){

            $checked[] = $designationId;

            $planTask = PlanTask::where('designation_id', $designationId)
                ->where('order_name_id', $orderId)
                ->first();

            if ($planTask) {
                return $planTask;
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
