<?php

namespace App\Livewire;

use App\Models\DeliveryNote;
use App\Models\Department;
use App\Models\OrderName;
use Livewire\Attributes\Session;
use Livewire\Component;
use Livewire\WithPagination;

class DeliveryNoteSearch extends Component
{
    use WithPagination;

    public $searchTerm;

    public ?DeliveryNote $editingDeliveryNote = null;
    public int $editFormVersion = 0;

    #[Session]
    public $selectedDepartmentSender;

    #[Session]
    public $selectedDepartmentReceiver;

    #[Session]
    public $selectedOrder;

    #[Session]
    public $selectedDocumentDate;

    public $route = 'delivery-notes';

    public function editDeliveryNote($id)
    {
        $this->editingDeliveryNote = DeliveryNote::with('designation')->findOrFail($id);
        $this->editFormVersion++;
        $this->resetValidation();
        $this->dispatch('delivery-note-edit-open');
    }

    public function mount()
    {
        if ((old('_delivery_note_create') || old('_delivery_note_edit')) && session()->has('errors')) {
            $this->setErrorBag(session('errors')->getBag('default'));
        }
        if (old('_delivery_note_edit')) {
            $this->editingDeliveryNote = DeliveryNote::with('designation')->find(old('_delivery_note_edit'));
        }
        if($this->selectedOrder==0) {

            $order_first = OrderName::where('is_order', 1)->orderBy('name')->first();
            if (isset($order_first->id)) {
                $this->selectedOrder = $order_first->id;
            }
        }

        if(!$this->selectedDepartmentSender) {
            $this->selectedDepartmentSender = Department::DEFAULT_FIRST_DEPARTMENT_ID;
        }

        if(!$this->selectedDepartmentReceiver) {
            $this->selectedDepartmentReceiver = Department::DEFAULT_SECOND_DEPARTMENT_ID;
        }

        if(!$this->selectedDocumentDate){
            $this->selectedDocumentDate = \Carbon\Carbon::now()->format('Y-m-d');
        }
    }

    public function deleteDeliveryNote($id)
    {
        $deliveryNote = DeliveryNote::findOrFail($id);
        $deliveryNote->delete();

        if ($this->editingDeliveryNote?->id === (int) $id) {
            $this->editingDeliveryNote = null;
        }
    }

    public function updateSearch()
    {
        $this->resetPage();
    }

    protected function departments()
    {
        return Department::whereIn('id',array(2,3,5))->get();
    }

    protected function documentDate()
    {
        return $this->selectedDocumentDate ?? \Carbon\Carbon::now()->format('Y-m-d');
    }

    protected function deliveryNotes()
    {
        $searchTerm = '%' . trim($this->searchTerm) . '%';

        if ($searchTerm == '%%') {

            return DeliveryNote
                ::with('orderName','senderDepartment','receiverDepartment','designation')
                ->orderBy('updated_at','desc')
                ->paginate(25);

        } else {

            return DeliveryNote
                ::whereHas('designation', function ($query) use ($searchTerm) {
                    $query->where('designation', 'like', $searchTerm)
                        ->orderByRaw("CAST(designation AS SIGNED)");
                })
                ->with('orderName','senderDepartment','receiverDepartment','designation')
                ->orderBy('updated_at','desc')
                ->paginate(25);
        }
    }

    public function render()
    {
        return view('livewire.delivery-note-search',[
            'items'=>$this->deliveryNotes(),
            'form_departments' => Department::all(),
            'last_record' => DeliveryNote::orderBy('updated_at', 'desc')->firstOrNew([]),
            'default_first_department' => Department::DEFAULT_FIRST_DEPARTMENT_ID,
            'default_second_department' => Department::DEFAULT_SECOND_DEPARTMENT_ID,
            'departments' => Department::whereIn('id',array(2,3,5))->get(),
            'document_date' => $this->documentDate(),
            'order_names' => OrderName::where('is_order',1)->orderBy('name')->get()
        ]);
    }
}
