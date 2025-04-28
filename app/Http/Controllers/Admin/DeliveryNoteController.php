<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeliveryNoteRequest;
use App\Models\DeliveryNote;
use App\Models\Department;
use App\Models\Order;
use App\Models\OrderName;
use App\Services\DeliveryNote\DeliveryNoteService;
use Illuminate\Http\Request;

class DeliveryNoteController extends Controller
{
    /*INSERT INTO plan_tasks (
    order_name_id,
    category_code,
    designation_id,
    order_designationEntry,
    order_designationEntry_letters,
    quantity,
    quantity_total,
    with_purchased,
    tm,
    sender_department_id,
    receiver_department_id,
       created_at,
    updated_at
)
SELECT
    38 AS order_name_id,
    pt.category_code,
    pt.designation_id,
    pt.order_designationEntry,
    pt.order_designationEntry_letters,
    pt.quantity,
    pt.quantity*20 as quantity_total,
    pt.with_purchased,
    pt.tm,
    pt.sender_department_id,
    pt.receiver_department_id,
    NOW() AS created_at,
    NOW() AS updated_at

FROM plan_tasks pt
WHERE pt.order_name_id = 19
*/
    public $route = 'delivery-notes';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administrator::include.delivery-notes.index', [
            'route' => $this->route,
            'order_names' => OrderName::where('is_order',1)->orderBy('name')->get(),
            'livewire_search' => 'delivery-note-search',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('administrator::include.delivery-notes.create', [
            'departments' => Department::all(),
            'order_names' => OrderName::where('is_order',1)->orderBy('name')->get(),
            'last_record' => DeliveryNote::orderBy('updated_at','desc')->firstOrNew([]),
            'route' => $this->route]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeliveryNoteRequest $request, DeliveryNoteService $service)
    {
        $service->store($request);

        return redirect()->route('delivery-notes.index')->with('status', 'Дані успішно збережено');

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
    public function edit(DeliveryNote $deliveryNote)
    {
        $orders = OrderName::where('is_order',1)->orderBy('name')->get();

        return view('administrator::include.delivery-notes.edit',[
            'route' => $this->route,
            'departments' => Department::all(),
            'item' => $deliveryNote,
            'order_names' => $orders
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeliveryNote $deliveryNote, DeliveryNoteService $service)
    {
        $service->update($request,$deliveryNote);
        return redirect()->route($this->route.'.index')->with('status', 'Дані успішно збережено');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
