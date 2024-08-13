<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Order;
use App\Models\OrderName;
use App\Models\ReportApplicationStatement;
use Livewire\Attributes\Session;
use Livewire\Component;
use App\Models\PlanTask;
use Livewire\WithPagination;

class PlanTaskTable extends Component
{
    use WithPagination;

    public $isProcessing = false;

    #[Session]
    public $selectedOrder;

    public $order_number;

    #[Session]
    public $sender_department_id;

    #[Session]
    public $receiver_department_id;

    public $sender_department;

    public $receiver_department;

    public $default_department = Department::DEFAULT_DEPARTMENT;

    public $route = 'plan-tasks';

    public function mount()
    {
        if(!$this->selectedOrder) {

            $order_first = OrderName::where('is_order', 1)->orderBy('name')->first();

            if (isset($order_first->id)) {
                $this->selectedOrder = $order_first->id;
            }
        }
        if(!$this->sender_department_id){
            $this->sender_department_id = $sender_department_id ?? Department::DEFAULT_FIRST_DEPARTMENT_ID;
        }
        if(!$this->receiver_department_id) {
            $this->receiver_department_id = $receiver_department_id ?? Department::DEFAULT_SECOND_DEPARTMENT_ID;
        }
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
        $department1 = Department::where('id', $this->sender_department_id)->first();
        $department2 = Department::where('id', $this->receiver_department_id)->first();
        $this->sender_department = $department1->number;
        $this->receiver_department = $department2->number;
        $order = OrderName
            ::where('id',$this->selectedOrder)
            ->first();
        if(isset($order->name)){
            $this->order_number = $order->name;
        }
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
            ::selectRaw('designation_entry_id, order_designationEntry, order_designationEntry_letters, order_name_id,category_code, SUM(quantity) as quantity, SUM(quantity_total) as quantity_total, tm')
            ->where('order_name_id',$this->selectedOrder)
            ->whereRaw('SUBSTR(tm, 1, 2) = ?', [$this->sender_department])
            ->whereRaw('SUBSTR(tm, -2) = ?', [$this->receiver_department])
            ->groupBy('designation_entry_id', 'order_name_id','order_designationEntry', 'order_designationEntry_letters','category_code', 'tm')
            ->get();

       foreach($details as $detail){

           $attributes = [
               'order_name_id' => $detail->order_name_id,
               'order_id' => $detail->order_id,
               'designation_id' => $detail->designation_entry_id,
               'tm' => $detail->tm
           ];

           $values = [
               'category_code' => $detail->category_code,
               'quantity' => $detail->quantity,
               'quantity_total' => $detail->quantity_total,
               'tm' => $detail->tm,
               'sender_department_id' => $this->sender_department_id,
               'receiver_department_id' => $this->receiver_department_id,
               'order_designationEntry' => $detail->order_designationEntry ,
               'order_designationEntry_letters' => $detail->order_designationEntry_letters,
               'is_report_application_statement' => 1
           ];

            PlanTask::firstOrCreate($attributes, $values);

        }

        $this->isProcessing = false;

        $this->dispatch('close-modal',name:'viewLog');

    }

    protected function planTasks()
    {
        return PlanTask
            ::where('order_name_id', $this->selectedOrder)
            ->where('sender_department_id', $this->sender_department_id)
            ->where('receiver_department_id', $this->receiver_department_id)
            ->with('designationEntry')
            ->orderBy('updated_at','desc')
            ->orderBy('order_designationEntry_letters')
            ->orderBy('order_designationEntry')
            ->paginate(25);;
    }

    public function render()
    {
        return view('livewire.plan-task-table',[
            'order_names'=> OrderName::where('is_order', 1)->orderBy('name')->get(),
            'departments' => Department::all(),
            'items' => $this->planTasks(),
            ]);
    }
}
