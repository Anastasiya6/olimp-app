<?php

namespace App\Livewire;

use App\Models\DeliveryNote;
use App\Models\Department;
use App\Models\Order;
use App\Models\OrderName;
use Livewire\Component;
use Livewire\WithPagination;

class DeliveryNoteSearch extends Component
{
    use WithPagination;

    public $selectedDepartmentSender;

    public $selectedDepartmentReceiver;

    public $selectedOrder;

    public $route = 'delivery-notes';

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
    }

    public function deleteDeliveryNote($id)
    {
        $deliveryNote = DeliveryNote::findOrFail($id);
        $deliveryNote->delete();

        // Отправить сообщение об успешном удалении
        session()->flash('message', 'Запис успішно видалено.');
    }

    public function render()
    {
        $items = DeliveryNote
            ::with('order_name')
            ->orderBy('updated_at','desc')
            ->paginate(25);

        return view('livewire.delivery-note-search',[
            'items'=>$items,
            'default_first_department' => Department::DEFAULT_FIRST_DEPARTMENT_ID,
            'default_second_department' => Department::DEFAULT_SECOND_DEPARTMENT_ID,
            'departments' => Department::whereIn('id',array(2,3,5))->get(),
            'order_names' => OrderName::where('is_order',1)->orderBy('name')->get()
        ]);
    }
}
