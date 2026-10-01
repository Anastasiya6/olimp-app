<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderName;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public $route = 'orders';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administrator::include.orders.index', ['title' => 'Розузловання']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('administrator::include.orders.create',[
            'order_names' => OrderName::all(),
            'route' => $this->route ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, OrderService $service)
    {
        $service->store($request);

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
    public function edit(Order $order)
    {
        return view('administrator::include.orders.edit', [
            'item' => $order,
            'order_names' => OrderName::all(),
            'designation' => $order->designation->designation,
            'route' => $this->route

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order, OrderService $service)
    {
        $service->update($request, $order);
        return redirect()->route($this->route.'.index')->with('status', 'Дані успішно збережено');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
       // ReportApplicationStatement::where('order_id', $order->id)->delete();
        $order->delete();
        return redirect()->route($this->route.'.index')->with('status', 'Дані успішно збережено');

    }
}
