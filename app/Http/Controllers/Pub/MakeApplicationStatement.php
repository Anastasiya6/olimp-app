<?php

namespace App\Http\Controllers\Pub;

use App\Http\Controllers\Controller;
use App\Services\Statements\ApplicationStatementService;

class MakeApplicationStatement extends Controller
{
    public function makeApplicationStatement(ApplicationStatementService $service)
    {
        $service->make();

        //session()->flash('success', 'Успешное выполнение операции');

        return redirect()->route('home');
    }
}
