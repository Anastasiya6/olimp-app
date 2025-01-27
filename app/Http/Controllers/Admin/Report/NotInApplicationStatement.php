<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\NotInApplicationStatementService;

class NotInApplicationStatement extends Controller
{
    public function notInApplicationStatement(NotInApplicationStatementService $service){

        $service->notInApplicationStatement();

    }

}
