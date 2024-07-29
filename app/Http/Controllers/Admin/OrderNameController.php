<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderNameCreateRequest;
use App\Models\OrderName;
use App\Services\OrderName\OrderNameService;
use Illuminate\Http\Request;

class OrderNameController extends Controller
{
    public $route = 'order-names';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administrator::include.order-names.index', [
            'route' => $this->route,
            'title' => 'Замовлення',
            'items' => OrderName::orderBy('created_at','desc')->paginate(25)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('administrator::include.order-names.create',[
            'route' => $this->route ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderNameCreateRequest $request, OrderNameService $service)
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
    public function edit(OrderName $orderName)
    {
        return view('administrator::include.order-names.edit', [
            'item' => $orderName,
            'route' => $this->route

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderNameCreateRequest $request, OrderName $orderName, OrderNameService $service)
    {
        $service->update($request, $orderName);
        return redirect()->route($this->route.'.index')->with('status', 'Дані успішно збережено');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderName $orderName)
    {
        $orderName->delete();
        return redirect()->route($this->route.'.index')->with('status', 'Дані успішно збережено');

        //ReportApplicationStatement::where('order_number', $order->order_number)->delete();
    }
}
