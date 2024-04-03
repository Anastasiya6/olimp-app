<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\EntryDetailService;

class EntryDetailController extends Controller
{
    public function entryDetail(EntryDetailService $service)
    {
        $service->entryDetail('ААМВ468369004-08');
    }
}
