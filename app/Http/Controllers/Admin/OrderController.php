<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderName;
use App\Services\Order\OrderService;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public $route = 'orders';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         return view('administrator::include.orders.index', [
            'items' => Order::orderBy('updated_at','desc')->orderBy('order_name_id','desc')->with('orderName','designation')->paginate(25),
            'route' => $this->route,
            /*'report_dates' => Order::leftJoin('report_application_statements', 'orders.order_number', '=', 'report_application_statements.order_number')
                ->select('orders.order_number', DB::raw('MIN(report_application_statements.created_at) as min_created_at'))
                ->groupBy('orders.order_number')
                ->pluck('min_created_at', 'orders.order_number')
                ->toArray(),*/
             'report_dates' => Order::leftJoin('report_application_statements', function (JoinClause $join) {
                 $join->on('orders.order_name_id', '=', 'report_application_statements.order_name_id')
                     ->on('orders.designation_id', 'report_application_statements.designation_id')
                     ->on('orders.designation_id', 'report_application_statements.designation_entry_id');
             })
                 ->pluck('report_application_statements.created_at', 'orders.id')
                 ->toArray(),
            'title' => 'Замовлення']);
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
