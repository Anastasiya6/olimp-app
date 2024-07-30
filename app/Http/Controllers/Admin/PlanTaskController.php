<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanTaskCreateRequest;
use App\Models\PlanTask;
use App\Services\PlanTask\PlanTaskService;
use Illuminate\Http\Request;

class PlanTaskController extends Controller
{
    public $route = 'plan-tasks';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $order_name_id = session('order_name_id');
        return view('administrator::include.plan_tasks.index',[
            'order_name_id' => $order_name_id,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($order_number)
    {
        //dd($order_number);

        return view('administrator::include.plan_tasks.create',[
            'route' => $this->route,
            'order_number' => $order_number
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlanTaskCreateRequest $request, PlanTaskService $service)
    {
      //  dd($request);
        $service->store($request);
        session(['order_name_id' => $request->order_name_id]);
        return redirect()->route($this->route.'.index')->with('status', 'Дані успішно збережено');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( PlanTask $planTask,$order_number)
    {
        return view('administrator::include.plan_tasks.edit',[
            'item' => $planTask,
            'order_number' => $order_number,
            'route' => $this->route
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PlanTask $planTask)
    {
        $planTask->quantity = $request->quantity;
        $planTask->tm = $request->tm;
        $planTask->save();
        session(['order_number' => $request->order_number]);
        return redirect()->route($this->route.'.index')->with('status', 'Дані успішно збережено');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlanTask $planTask)
    {

    }

    public function pi0Pdf(PlanTaskService $service)
    {
        $service->plan_task();
    }
}
