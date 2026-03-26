<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeliveryNoteRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Models\DeliveryNote;
use App\Models\Department;
use App\Models\MaterialIssuance;
use App\Models\Order;
use App\Models\OrderName;
use App\Models\Task;
use App\Services\DeliveryNote\ImportMaterialStock;
use App\Services\Task\TaskService;
use Illuminate\Http\Request;

class IssuanceMaterialController extends Controller
{
    public $route = 'issuance-materials';
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('administrator::include.issuance-materials.index', [
            'route' => $this->route,
            'livewire_search' => 'issuance-material-search',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('administrator::include.issuance-materials.create', [
            'order_names' => OrderName::where('is_order',1)->orderBy('name')->get(),
//            'sender_department_id' => $sender_department_id,
//            'sender_department' => Department::where('id', $sender_department_id)->first()->number,
            'route' => $this->route]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //dd($request);
        //$service->store($request);

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
    public function edit(MaterialIssuance $materialIssuance)
    {
        return view('administrator::include.issuance-materials.edit',[
            'route' => $this->route,
            'item' => $materialIssuance,
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
