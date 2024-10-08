<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanTaskCreateRequest;
use App\Models\Department;
use App\Models\OrderName;
use App\Models\PlanTask;
use App\Services\PlanTask\PlanTaskService;
use App\Services\Reports\PlanTaskSpecificationNormService;
use App\Services\Reports\ReportPlanTaskService;
use Illuminate\Http\Request;

class PlanTaskController extends Controller
{
    public $route = 'plan-tasks';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administrator::include.plan-tasks.index',[
            'order_name_id' => session('order_name_id'),
            'sender_department_id' => session('sender_department_id'),
            'receiver_department_id' => session('receiver_department_id'),

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($order_name_id,$sender_department_id,$receiver_department_id)
    {
        $order_number = '';
        $order_name_quantity = 0;
        $order = OrderName
            ::where('id',$order_name_id)
            ->first();

        if(isset($order->name)){
            $order_number = $order->name;
            $order_name_quantity = $order->quantity;
        }

        return view('administrator::include.plan-tasks.create',[
            'route' => $this->route,
            'order_name_quantity' => $order_name_quantity,
            'order_name_id' => $order_name_id,
            'sender_department_id' => $sender_department_id,
            'receiver_department_id' => $receiver_department_id,
            'order_number' => $order_number,
            'sender_department' => Department::where('id', $sender_department_id)->first()->number,
            'receiver_department' => Department::where('id', $receiver_department_id)->first()->number,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlanTaskCreateRequest $request, PlanTaskService $service)
    {
        $service->store($request);
        session(['order_name_id' => $request->order_name_id,'sender_department_id' => $request->sender_department_id,'receiver_department_id' => $request->receiver_department_id]);
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
    public function edit(PlanTask $plan_task)
    {
       return view('administrator::include.plan-tasks.edit',[
            'item' => $plan_task,
            'order_name_id' => $plan_task->order_name_id,
            'sender_department_id' => $plan_task->sender_department_id,
            'receiver_department_id' => $plan_task->receiver_department_id,
            'order_number' => $plan_task->orderName->name,
            'route' => $this->route,
            'sender_department' => Department::where('id', $plan_task->sender_department_id)->first()->number,
            'receiver_department' => Department::where('id', $plan_task->receiver_department_id)->first()->number,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PlanTask $planTask, PlanTaskService $service)
    {
        $service->update($planTask,$request);

        session(['order_name_id' => $request->order_name_id,'sender_department_id' => $request->sender_department_id,'receiver_department_id' => $request->receiver_department_id]);
        return redirect()->route($this->route.'.index')->with('status', 'Дані успішно збережено');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlanTask $planTask)
    {

    }

    public function planTaskPdf($order_name_id,$sender_department,$receiver_department,ReportPlanTaskService $service)
    {
        $service->plan_task($order_name_id,$sender_department,$receiver_department);
    }

    public function exportExcel($order_name_id,$sender_department_id, PlanTaskSpecificationNormService $service)
    {
        $fileName = $service->exportExcel($order_name_id,$sender_department_id);

        // Возврат файла для скачивания (опционально)
        return response()->download($fileName)->deleteFileAfterSend(true);
    }
}
