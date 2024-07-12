<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Order;
use App\Services\Reports\EntryDetailService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Session;
use Livewire\Component;

class ReportTable extends Component
{
    public $selectedDepartment;

    #[Session]
    public $selectedDepartmentEntry;

    public $order_number;

    #[Session]
    public $designation_number;

    public $isProcessing = false;

    public function mount()
    {
        $this->selectedDepartment = Department::DEFAULT_DEPARTMENT;

        $this->selectedDepartmentEntry = Department::DEFAULT_DEPARTMENT;

    }

    public function generateReportEntryDetailSpecification()
    {
       if (empty($this->designation_number)) {
            session()->flash('error', 'Будь ласка, введіть номер вузла.');
           // $this->dispatch('alertRemove'); // Дополнительно: если используете JavaScript для алертов
            $this->dispatch('message', [
                'text' => 'Please Login To Continue',
                'type' => 'danger',
                'status' => 401
            ]);
            return;
        }
        return  $this->redirectRoute('entry.detail.designation', ['designation_number' => trim($this->designation_number),'department' => $this->selectedDepartmentEntry]);
    }

    public function generateReport($order_number,EntryDetailService $service)
    {
        $this->order_number = $order_number;

        $order = Order::where('order_number',$order_number)->first();

        if($order){

            $this->isProcessing = true;

            $pdf_path = $service->entryDetail($order->designation->designation,$order_number);

            $this->isProcessing = false;

            return response()->download($pdf_path, 'entry_detail_order_'.$this->order_number.'.pdf', [
                'Content-Type' => 'application/pdf',
            ]);
        }
        //$pdf->Output('entry_detail.pdf', 'I');

    }
    public function render()
    {
        $items = Order::whereIn('order_number', function($query) {
            $query->select('order_number')
                ->from('report_application_statements')
                ->groupBy('order_number');
        })
            ->leftJoin('designations', 'orders.designation_id', '=', 'designations.id') // Додаємо приєднання до таблиці designations
            ->select('order_number',
                DB::raw('SUM(quantity) as quantity'),
                DB::raw('count(quantity) as count_quantity'),
                DB::raw('MAX(orders.updated_at) as updated_at'), DB::raw('MIN(designations.designation) as designation'))
            ->groupBy('order_number')
            ->orderBy('updated_at','desc')
            ->paginate(25);

       /*$items = Order::whereIn('order_number', function($query) {
                    $query->select('order_number')
                        ->from('report_application_statements')
                        ->groupBy('order_number');
                })
            ->orderBy('updated_at','desc')
            ->with('designation')
            ->paginate(25);*/
        $departments = Department::all();
        $default_department = Department::DEFAULT_DEPARTMENT;


        return view('livewire.report-table',compact('items','departments','default_department'));
    }
}
