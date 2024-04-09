<?php


namespace App\Services\Statements;


use App\Models\Order;
use App\Models\ReportApplicationStatement;
use App\Models\Specification;
use Illuminate\Support\Facades\DB;

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
                'tm' => $order->designation->route . "-99",
                'tm1' => 68,
                'hcp' => 0,
                'order_designationEntry' => $this->getNumbers( $order->designation->designation),
                'order_designation' => $this->getNumbers($order->designation->designation),
                'order_designationEntry_letters' => $this->getLetters($order->designation->designation)
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

        $specifications = Specification::with('designations')
            ->where('designation_id', $find_designation_id)
            //->where('category_code', '!=', '')
            ->orderBy('designation_id')
            ->orderBy('designation_entry_id')
            ->get();

        foreach ($specifications as $specification) {

            $hcp = SUBSTR($specification->designations->route,0,2);
            $tm = 0;

            if(isset($specification->designationEntry) && isset($specification->designations)) {
               if ($specification->designationEntry->route == "" && $specification->designations->route != "") {
                   $tm = "99";
               } elseif ($specification->designationEntry->id == $specification->designations->id && $specification->designationEntry->route == "" && $specification->designations->route == "") {
                   $tm = "99";
               } elseif (substr($specification->designationEntry->route, 0, 2) == substr($specification->designations->route, 0, 2) && $specification->designationEntry->route != "") {
                   $tm = $specification->designationEntry->route;
               } elseif (substr($specification->designationEntry->route, -2) == $specification->designations->route) {
                   $tm = $specification->designationEntry->route;
               } elseif (substr($specification->designations->route, 0, 2) != "") {
                   $tm = $specification->designationEntry->route . "-99";
               } elseif (substr($specification->designations->route, 0, 2) == "") {
                   $tm = $specification->designationEntry->route;
               }

            }

            ReportApplicationStatement::updateOrCreate([
                'designation_entry_id' => $specification->designation_entry_id,
                'designation_id' => $specification->designation_id,
                'order_number' => $order_number,
                'category_code' => $specification->category_code,
                ],[
                'quantity' => $specification->quantity,
                'quantity_total' => DB::raw('quantity_total + '.$specification->quantity * $quantity,),// * $quantity,
                'tm' => $tm,
                'tm1' => self::DEPARTMENT_RECEPIENT,
                'hcp' => $hcp,
                'order_designationEntry' => $specification->designationEntry ? $this->getNumbers($specification->designationEntry->designation) : '',
                'order_designation' => $specification->designations ? $this->getNumbers($specification->designations->designation) : '',
                'order_designationEntry_letters' => $specification->designationEntry ? $this->getLetters($specification->designationEntry->designation) : ''

            ]);

            if ($specification->category_code == 0 || $specification->category_code == 1) {

                $this->disassembly($specification->designation_entry_id, $specification->quantity * $quantity, $order_number);
            }
        }
    }
    public function getNumbers($designation)
    {
        return preg_replace('/[^0-9]+/', '', $designation);

    }
    public function getLetters($designation)
    {
        return preg_replace('/[^А-Яа-я]+/', '', $designation);

    }

}

