<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\ApplicationStatementService;

class ReportDepartmentList extends Controller
{
    public function reportDepartmentList($filter,$order_name_id,$department, applicationStatementService $service){

        $service->applicationStatement( $filter,$order_name_id, $department);

    }

}
