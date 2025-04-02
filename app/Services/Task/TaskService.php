<?php

namespace App\Services\Task;
use App\Models\Task;
use App\Models\Designation;
use Illuminate\Http\Request;

class TaskService
{
    public function store(Request $request)
    {
        $designation = Designation::where('designation', $request->designation)->first();

        if ($designation->id) {
            $task = new Task();
            $task->designation_id = $designation->id;
            $task->quantity =  $request->quantity;
            $task->department_id =  $request->department_id;
            $task->save();
        }
    }

    public function update(Request $request, Task $task)
    {
        $task->department_id = $request->department_id;
        $task->quantity = $request->quantity;
        $task->save();
    }

}
