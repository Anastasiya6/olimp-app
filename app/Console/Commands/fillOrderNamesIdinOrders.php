<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderName;
use Illuminate\Console\Command;

class fillOrderNamesIdinOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fill-order-names-idin-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::distinct()->pluck('order_number','order_number');
        $orders->each(function ($orderNumber) {
            OrderName::updateOrCreate(['name' => $orderNumber]);
        });
        $order_ids = OrderName::pluck('id','name');
        echo print_r($order_ids,1);

        Order::all()->each(function ($order) use ($order_ids) {
            if (isset($order_ids[$order->order_number])) {
                $order->update(['order_name_id' => $order_ids[$order->order_number]]);
            }
        });
    }
}
