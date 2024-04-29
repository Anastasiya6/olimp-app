<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\ReportApplicationStatement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class UpdateDataReport extends Component
{
    use withPagination;

    public $reportDates = [];

    public $order_number;

    public $report_date;

    public $report_dates = [];

    public $route = 'orders';

    protected $listeners = ['reportGenerated' => 'updateReportDate'];

    public function mount($order_number,$report_date)
    {
        $this->report_dates[$order_number] = $report_date;

        $this->order_number = $order_number;
    }

    public function updateReportDate($order_number,$report_date)
    {
        $this->report_dates[$order_number] = $report_date;
    }

    public function render()
    {
        $items = Order::paginate(25);

        $route = $this->route;

        $in_report = ReportApplicationStatement
                    ::select('order_number',DB::raw('MIN(created_at) as min_created_at'))
                    ->groupBy('order_number')
                    ->pluck('min_created_at','order_number')
                    ->toArray();
        return view('livewire.update-data-report',compact(      'items',
                                                                'in_report',
                                                                            'route'));
    }
}
