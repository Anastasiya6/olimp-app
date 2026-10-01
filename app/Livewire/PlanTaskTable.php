<?php

namespace App\Livewire;

use App\Models\DeliveryNote;
use App\Models\Department;
use App\Models\Order;
use App\Models\OrderName;
use App\Models\ReportApplicationStatement;
use App\Services\HelpService\NoMaterialService;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Session;
use Livewire\Component;
use App\Models\PlanTask;
use Livewire\WithPagination;

class PlanTaskTable extends Component
{
    use WithPagination;

    public $isProcessing = false;

    public ?PlanTask $editingPlanTask = null;
    public bool $planFormLoaded = false;
    public int $planFormVersion = 0;
    public array $planFormData = [];

    public function openPlanForm($id = null)
    {
        $this->editingPlanTask = $id ? PlanTask::with('designation', 'orderName')->findOrFail($id) : null;
        $orderId = $this->editingPlanTask?->order_name_id ?? $this->selectedOrder;
        $senderId = $this->editingPlanTask?->sender_department_id ?? $this->sender_department_id;
        $receiverId = $this->editingPlanTask?->receiver_department_id ?? $this->receiver_department_id;
        $order = OrderName::find($orderId);
        $this->planFormData = [
            'order_name_id' => $orderId,
            'order_number' => $order?->name ?? '',
            'order_name_quantity' => $order?->quantity ?? 0,
            'sender_department_id' => $senderId,
            'receiver_department_id' => $receiverId,
            'sender_department' => Department::find($senderId)?->number ?? '',
            'receiver_department' => Department::find($receiverId)?->number ?? '',
        ];
        $this->planFormLoaded = true;
        $this->planFormVersion++;
        $this->resetValidation();
        $this->dispatch('plan-form-open');
    }


    public $searchTerm;

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

    public $with_purchased = 0;

    public $with_material_purchased = 0;

    public $flag = 0;

    public $from_order_id = '';

    public $to_order_id = '';

    //public $selectedItems = [];

    public function mount()
    {
        if(!$this->selectedOrder) {

            $order_first = OrderName::where('is_order', 1)->orderBy('name')->first();

            if (isset($order_first->id)) {
                $this->selectedOrder = $order_first->id;
            }
        }
        if(!$this->sender_department_id){
            $this->sender_department_id = $sender_department_id ?? Department::DEPARTMENT_25_ID;
        }
        if(!$this->receiver_department_id) {
            $this->receiver_department_id = $receiver_department_id ?? 0;
        }
        if (old('_plan_form') && session()->has('errors')) {
            $this->selectedOrder = old('order_name_id', $this->selectedOrder);
            $this->sender_department_id = old('sender_department_id', $this->sender_department_id);
            $this->receiver_department_id = old('receiver_department_id', $this->receiver_department_id);
            $this->openPlanForm(old('_plan_edit') ?: null);
            $this->setErrorBag(session('errors')->getBag('default'));
        }
    }

    public function deletePlanTask($id)
    {
        $planTask = PlanTask::findOrFail($id);
        $planTask->delete();

        if ($this->editingPlanTask?->id === (int) $id) {
            $this->editingPlanTask = null;
            $this->planFormLoaded = false;
        }
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

    public function viewConfirmFromOrder()
    {
        $this->dispatch('open-modal',name:'viewOrderFromOrder');
    }

    public function updateSearch()
    {
        $this->flag = 0;
        $this->with_purchased = (int) $this->with_purchased;
        $this->with_material_purchased = (int) $this->with_material_purchased;
        $this->resetPage();
    }

    public function makeFromOrderToOrder()
    {
        $this->isProcessing = true;

        if (!$this->from_order_id || !$this->to_order_id) {
            session()->flash('error', 'Виберіть замовлення, з якого і на яке потрібно перенести план.');
            $this->isProcessing = false;
            return;
        }

        $itemsTo = PlanTask::where('order_name_id', $this->to_order_id)->exists();

        if ($itemsTo) {
            session()->flash('error', 'На цьому замволенні вже є план.');
            $this->isProcessing = false;
            return;
        }

        $itemsFrom = PlanTask::where('order_name_id', $this->from_order_id)->get();

        $planCount = OrderName::find($this->to_order_id)->quantity; // кількість комплектів

        foreach ($itemsFrom as $item) {
            $newItem = $item->replicate();

            $newItem->order_name_id = $this->to_order_id;

            // quantity переносимо без змін
            $newItem->quantity = $item->quantity;

            // quantity_total перераховуємо
            $newItem->quantity_total = $item->quantity * $planCount;

            $newItem->save();
        }

        $this->isProcessing = false;

        $this->dispatch('close-modal',name:'viewOrderFromOrder');
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

        $order = OrderName
            ::where('id',$this->selectedOrder)
            ->first();

        $order_name_quantity = 0;

        if(isset($order->quantity)){
            $order_name_quantity = $order->quantity;
        }
        $order_name_quantity = $order_name_quantity == 0  ? 1 : $order_name_quantity;

       foreach($details as $detail){

           $attributes = [
               'order_name_id' => $detail->order_name_id,
               'order_id' => $detail->order_id,
               'designation_id' => $detail->designation_entry_id,
               //'tm' => $detail->tm
           ];

           $values = [
               'category_code' => $detail->category_code,
               'quantity' => $detail->quantity_total,
               'quantity_total' => $detail->quantity_total * $order_name_quantity ,
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
/**/
    protected function planTasks()
    {
        $items = $this->getPlanTasks();

        $selected_department_number = Department::where('id',$this->sender_department_id)->first()?->number;

        foreach($items as $item){

            $item->material = 1;

            if($item->designationMaterial->isEmpty()){
                $item->material = 0;
            }
            $item->material = NoMaterialService::noMaterial($item->designation_id,$item->designationMaterial->isNotEmpty(),1,$selected_department_number);

        }
        return $items;
    }

    protected function getPlanTasks()
    {
        $searchTerm = '%' . trim($this->searchTerm) . '%';

        if ($searchTerm == '%%') {
            return PlanTask
                ::where('order_name_id', $this->selectedOrder)
                ->where('sender_department_id', $this->sender_department_id)
                ->when($this->receiver_department_id != 0, function ($query) {
                    return $query->where('receiver_department_id', $this->receiver_department_id);
                })
                ->with('designationEntry')
                ->orderBy('updated_at','desc')
                ->orderBy('order_designationEntry_letters')
                ->orderBy('order_designationEntry')
                //->get();
                ->paginate(25);

        }else{
            return PlanTask
                ::where('order_name_id', $this->selectedOrder)
                ->where('sender_department_id', $this->sender_department_id)
                ->when($this->receiver_department_id != 0, function ($query) {
                    return $query->where('receiver_department_id', $this->receiver_department_id);
                })
                ->whereHas('designation', function ($query) use ($searchTerm) {
                    $query->where('designation', 'like', $searchTerm)
                        ->orderByRaw("CAST(designation AS SIGNED)");
                })
                ->with('designationEntry')
                ->orderBy('updated_at','desc')
                ->orderBy('order_designationEntry_letters')
                ->orderBy('order_designationEntry')
                //->get();
                ->paginate(25);
        }
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
