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

    public $order_name_id;

    public $report_date;

    public $report_dates = [];

    public $route = 'orders';

    public $in_report;

    protected $listeners = ['reportGenerated' => 'updateReportDate'];

    public function mount($order_name_id,$report_date)
    {
        $this->report_dates[$order_name_id] = $report_date;

        $this->order_name_id = $order_name_id;
    }

    public function updateReportDate($order_name_id,$report_date)
    {
        $this->report_dates[$order_name_id] = $report_date;
    }

    public function render()
    {
        $items = Order::paginate(25);

        $route = $this->route;

        $in_report = ReportApplicationStatement
                    ::select('order_name_id',DB::raw('MIN(created_at) as min_created_at'))
                    ->groupBy('order_name_id')
                    ->pluck('min_created_at','order_name_id')
                    ->toArray();
        return view('livewire.update-data-report',compact(      'items',
                                                                'in_report',
                                                                            'route'));
    }
}
