<?php

namespace App\Livewire;

use App\Models\DeliveryNote;
use App\Models\Department;
use App\Models\Order;
use App\Models\OrderName;
use App\Models\Specification;
use App\Services\HelpService\NoMaterialService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class WriteOffSearch extends Component
{
    use WithPagination;

    public $route = 'write-offs';

    public $startDate;

    public $endDate;

    public $selectedOrder;

    public $order_number;

    public $selectedDepartmentSender;

    public $selectedDepartmentReceiver;

    public $selectedDeliveryNotes;

    public $selectedItems = [];

    public $flag = 0;

    public function mount()
    {
        if($this->selectedOrder==0) {

            $order_first = OrderName::where('is_order', 1)->orderBy('name')->first();

            if (isset($order_first->id)) {
                $this->selectedOrder = $order_first->id;
            }
        }

        $this->selectedDepartmentSender = Department::DEFAULT_FIRST_DEPARTMENT_ID;

        $this->selectedDepartmentReceiver = Department::DEFAULT_SECOND_DEPARTMENT_ID;

        $this->startDate =  Carbon::now()->format('Y-m-d');

        $this->endDate =  Carbon::now()->format('Y-m-d');
    }

    public function updateSearch()
    {

        $this->flag = 0;

    }

    public function viewConfirm()
    {
        $this->flag = 1;

        $this->order_number = OrderName::find($this->selectedOrder);

        if ($this->order_number) {
            $this->order_number = $this->order_number->name;
        }

        $this->selectedDeliveryNotes = DeliveryNote::whereIn('id',$this->selectedItems)->get();

        $this->dispatch('open-modal',name:'viewLog');

    }

    public function makeWriteOff()
    {
        $this->dispatch('close-modal',name:'viewLog');
        $this->selectedItems = [];
    }

    public function cancelWriteOff()
    {
        $this->dispatch('close-modal',name:'viewLog');
    }

    protected function deliveryNotes()
    {
        $items = DeliveryNote::withFilters( $this->startDate,
            $this->endDate,
            $this->selectedOrder,
            $this->selectedDepartmentSender,
            $this->selectedDepartmentReceiver)
            ->with('designationMaterial.material','designation','orderName','senderDepartment','receiverDepartment')
            ->orderBy('document_number')
            ->get();

        if($this->flag == 0){
            $this->selectedItems = [];
        }
        foreach($items as $item){

            $item->material = 1;

            if($item->designationMaterial->isEmpty()){
                $item->material = 0;
            }
            $item->material = NoMaterialService::noMaterial($item->designation_id,$item->designationMaterial->isNotEmpty());
            if($item->material && $this->flag == 0){
                $this->selectedItems[] = $item->id;
            }
        }

        return $items;
    }

    public function render()
    {
        return view('livewire.write-off-search',[
            'items'=>$this->deliveryNotes(),
            'default_first_department' => Department::DEFAULT_FIRST_DEPARTMENT_ID,
            'default_second_department' => Department::DEFAULT_SECOND_DEPARTMENT_ID,
            'departments' => Department::whereIn('id',array(2,3,5))->get(),
            'orders'=>OrderName::where('is_order',1)->orderBy('name')->get()]);
    }
}
