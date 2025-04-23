<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeliveryNoteRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Models\DeliveryNote;
use App\Models\Department;
use App\Models\Order;
use App\Models\OrderName;
use App\Models\Task;
use App\Services\DeliveryNote\DeliveryNoteService;
use App\Services\Task\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public $route = 'tasks';
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->type;
        return view('administrator::include.tasks.index', [
            'route' => $this->route,
            'type' => $type,
            'title_type' => $type == 'department' ? 'цех': 'технолог',
            'livewire_search' => 'task-search',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($sender_department_id,$type)
    {
        return view('administrator::include.tasks.create', [
            'departments' => Department::all(),
            'type' => $type,
            'sender_department_id' => $sender_department_id,
            'sender_department' => Department::where('id', $sender_department_id)->first()->number,
            'route' => $this->route]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request, TaskService $service)
    {
        $service->store($request);

        return redirect()->route($this->route.'.index',['type'=>$request->type])->with('status', 'Дані успішно збережено');

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
    public function edit($type,Task $task)
    {
        return view('administrator::include.tasks.edit',[
            'route' => $this->route,
            'type' => $type,
            'departments' => Department::all(),
            'item' => $task,
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task, TaskService $service)
    {
        $service->update($request,$task);
        return redirect()->route($this->route.'.index',['type'=>$request->type])->with('status', 'Дані успішно збережено');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
