<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Order;
use App\Models\OrderName;
use App\Models\ReportApplicationStatement;
use Livewire\Component;
use App\Models\PlanTask;
use Livewire\WithPagination;

class PlanTaskTable extends Component
{
    use WithPagination;

    public $isProcessing = false;

    public $selectedOrder;

    public $selectedDepartment1;

    public $selectedDepartment2;

    public $default_department;

    public $route = 'plan-tasks';

    public function mount()
    {
        $this->default_department = Department::DEFAULT_DEPARTMENT;

        $order_first = OrderName::where('is_order', 1)->orderBy('name')->first();

        if (isset($order_first->id)) {
            $this->selectedOrder = $order_first->id;
        }

        $this->selectedDepartment1 = Department::DEFAULT_DEPARTMENT;

        $this->selectedDepartment2 = Department::DEFAULT_DEPARTMENT;
    }

    public function deletePlanTask($id)
    {
        $planTask = PlanTask::findOrFail($id);
        $planTask->delete();

        // Отправить сообщение об успешном удалении
        session()->flash('message', 'Запис успішно видалено.');
    }

    public function viewConfirm()
    {
        $this->dispatch('open-modal',name:'viewLog');
    }

    public function updateSearch()
    {
        $this->resetPage();

    }

    public function makeFromDisassembly()
    {
       $this->isProcessing = true;

       $details = ReportApplicationStatement
            ::selectRaw('designation_entry_id, order_designationEntry, order_designationEntry_letters, order_number,category_code, SUM(quantity) as quantity, SUM(quantity_total) as quantity_total, tm')
            ->where('order_number',$this->selectedOrder)
            ->whereRaw('SUBSTR(tm, 1, 2) = ?', [$this->selectedDepartment1])
            ->whereRaw('SUBSTR(tm, -2) = ?', [$this->selectedDepartment2])
            ->groupBy('designation_entry_id', 'order_number','order_designationEntry', 'order_designationEntry_letters','category_code', 'tm')
            ->get();


       foreach($details as $detail){

           $attributes = [
               'order_number' => $detail->order_number,
               'designation_entry_id' => $detail->designation_entry_id,
               'tm' => $detail->tm
           ];

           $values = [
               'category_code' => $detail->category_code,
               'quantity' => $detail->quantity,
               'quantity_total' => $detail->quantity_total,
               'tm' => $detail->tm,
               'order_designationEntry' => $detail->order_designationEntry ,
               'order_designationEntry_letters' => $detail->order_designationEntry_letters,
               'is_report_application_statement' => 1
           ];

            PlanTask::firstOrCreate($attributes, $values);

        }

        //$service->make($this->order_number);

        $this->isProcessing = false;

        $this->dispatch('close-modal',name:'viewLog');

        //$this->dispatch('reportGenerated',$this->order_number,now()->toDateTimeString());
    }

    public function render()
    {
        $plan_tasks = PlanTask
            ::where('order_name_id', $this->selectedOrder)
            ->whereRaw('LEFT(tm, 2) = ?', [$this->selectedDepartment1])
            ->whereRaw('SUBSTR(tm, -2) = ?', [$this->selectedDepartment2])
            ->with('designationEntry')
            ->orderBy('order_designationEntry_letters')
            ->orderBy('order_designationEntry')
            ->paginate(25);

        return view('livewire.plan-task-table',[
            'order_names'=> OrderName::where('is_order', 1)->orderBy('name')->get(),
            'departments' => Department::all(),
            'items' => $plan_tasks]);
    }
}
