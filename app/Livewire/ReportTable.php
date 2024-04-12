<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Order;
use Livewire\Component;

class ReportTable extends Component
{
    public $selectedDepartment;

    public function mount()
    {
        $this->selectedDepartment = Department::DEFAULT_DEPARTMENT;
    }

    public function render()
    {
        $items = Order::whereIn('order_number', function($query) {
                    $query->select('order_number')
                        ->from('report_application_statements')
                        ->groupBy('order_number');
                })->paginate(25);
        $departments = Department::all();
        $default_department = Department::DEFAULT_DEPARTMENT;


        return view('livewire.report-table',compact('items','departments','default_department'));
    }
}
