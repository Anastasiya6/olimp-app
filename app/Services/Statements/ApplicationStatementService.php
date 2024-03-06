<?php


namespace App\Services\Statements;


use App\Models\Order;
use App\Models\ReportApplicationStatement;
use App\Models\Specification;

class ApplicationStatementService
{
    const DEPARTMENT_RECEPIENT = '68';

    public function make()
    {
        $orders = Order
            ::orderBy('designation_id')
            ->orderBy('category_code')
            ->get();

        ReportApplicationStatement::truncate();

        foreach($orders as $order) {
            //главная сборка, ту что указываем в заказе, которую хотим раскручивать
            ReportApplicationStatement::create([
                'designation_id' => $order->designation_id,
                'category_code' => $order->category_code,
                'designation_entry_id' => $order->designation_id,
                'quantity' => $order->quantity,
                'quantity_total' => $order->quantity,
                'order_number' => $order->order_number,
                'tm' => self::DEPARTMENT_RECEPIENT
            ]);
        }
        //zad
        $orders = Order::select('designation_id', 'category_code','order_number')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->groupBy('designation_id', 'category_code', 'order_number')
            ->get();

        foreach($orders as $order) {

            $this->disassembly($order->designation_id, $order->total_quantity, $order->order_number);
        }
      //  echo 'Програма виконана успішно';
    }

    public function disassembly($find_designation_id, $quantity, $order_number){

        $specifications = Specification
            ::where('designation_id', $find_designation_id)
            //->where('category_code', '!=', '')
            ->orderBy('designation_id')
            ->orderBy('designation_entry_id')
            ->get();

        foreach ($specifications as $specification) {

            ReportApplicationStatement::create([
                'designation_entry_id' => $specification->designation_entry_id,
                'category_code' => $specification->category_code,
                'designation_id' => $specification->designation_id,
                'quantity' => $specification->quantity,
                'quantity_total' => $specification->quantity * $quantity,
                'order_number' => $order_number,
                'tm' => $specification->route,
                'tm1' => self::DEPARTMENT_RECEPIENT,
                'hcp' => SUBSTR($specification->route,1,2)
            ]);
            if ($specification->category_code == 0 || $specification->category_code == 1) {

                $this->disassembly($specification->designation_entry_id, $specification->quantity * $quantity, $order_number);
            }
        }
    }

}

