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
        $statusCreatePlanTask = $service->store($request);

        return redirect()
            ->route('delivery-notes.index')
            ->with([
                'status' => 'Дані успішно збережено',
                //'message' => $statusCreatePlanTask
            ]);

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
