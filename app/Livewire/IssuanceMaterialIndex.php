<?php

namespace App\Livewire;

use App\Models\ImportMaterialStock;
use App\Models\MaterialIssuance;
use App\Models\MaterialIssuanceItem;
use App\Models\OrderName;
use App\Models\User;
use DB;
use Livewire\Attributes\Session;
use Livewire\Component;
use Livewire\WithPagination;

class IssuanceMaterialIndex extends Component
{
    use WithPagination;

    public $selectedItems = [];

    public ?int $editingDocumentId = null;
    public bool $documentFormLoaded = false;
    public int $documentFormVersion = 0;

    public function openDocument(?int $id = null)
    {
        if ($id !== null) {
            MaterialIssuance::findOrFail($id);
        }
        $this->editingDocumentId = $id;
        $this->documentFormLoaded = true;
        $this->documentFormVersion++;
        $this->dispatch('issuance-document-open');
    }


    #[Session]
    public $designation_number;

    public $selectedOrder = null;

    public $filterPlanDesignation = '';

    public $filterDocumentNumber = '';

    public $filterOrder = '';

    public function updatedFilterPlanDesignation()
    {
        $this->resetSearchPage();
    }

    public function updatedFilterOrder()
    {
        $this->resetSearchPage();
    }

    public function updatedFilterDocumentNumber()
    {
        $this->resetSearchPage();
    }

    public function resetFilters()
    {
        $this->reset('filterPlanDesignation', 'filterDocumentNumber', 'filterOrder');
        $this->resetSearchPage();
    }

    private function resetSearchPage()
    {
        $this->resetPage();
        $this->selectedItems = [];
    }

    public function mount()
    {
        if($this->designation_number == '')
            $this->designation_number = 'ААМВ685614100';

    }

    public function postDocument($id)
    {
        $doc = MaterialIssuance::findOrFail($id);

        if ($doc->status === 'posted') {
            return;
        }

        // отримати items
        $items = MaterialIssuanceItem::where('material_issuance_id', $doc->id)->get();

        foreach ($items as $item) {

            // списання зі складу (твій сервіс)
            ImportMaterialStock::create([
                'import_material_id' => $item->import_material_id,
                'amount' => -$item->quantity,
                'type' => 'stock_out',
                'document_number' => $item->material_issuance_id
            ]);
        }

        $doc->update([
            'status' => 'posted'
        ]);
    }

    public function unpostDocument($id)
    {
        $doc = MaterialIssuance::findOrFail($id);

        if ($doc->status !== 'posted') {
            return;
        }

        $items = MaterialIssuanceItem::where('material_issuance_id', $doc->id)->get();

        DB::transaction(function () use ($doc, $items) {

            foreach ($items as $item) {

                ImportMaterialStock::create([
                    'import_material_id' => $item->import_material_id,
                    'amount' => $item->quantity, // 🔥 ПЛЮС замість мінуса
                    'type' => 'stock_in',
                    'document_number' => $doc->id
                ]);
            }

            $doc->update([
                'status' => 'draft'
            ]);
        });
    }

    public function render()
    {
        $order_names = OrderName::where('is_order', 1)->orderBy('name')->get();

        if (!$this->selectedOrder && $order_names->count()) {
            $this->selectedOrder = $order_names->first()->id;
        }

        return view('livewire.issuance-material-index', [
            'order_names'=> $order_names,
            'recipients' => User::orderBy('name')->get(['id', 'name']),
            'items' => MaterialIssuance::with('items', 'receivedByUser', 'order_name', 'planTaskDesignation', 'designation')
                ->byDesignation()
                ->whereHas('items')
                ->when($this->filterOrder !== '', function ($query) {
                    $query->where('order_name_id', $this->filterOrder);
                })
                ->when(trim($this->filterDocumentNumber) !== '', function ($query) {
                    $query->where('id', (int) trim($this->filterDocumentNumber));
                })
                ->when(trim($this->filterPlanDesignation) !== '', function ($query) {
                    $search = '%'.trim($this->filterPlanDesignation).'%';

                    $query->where(function ($searchQuery) use ($search) {
                        $searchQuery->whereHas('planTaskDesignation', function ($designationQuery) use ($search) {
                            $designationQuery->where('designation', 'like', $search);
                        })->orWhereHas('receivedByUser', function ($recipientQuery) use ($search) {
                            $recipientQuery->where('name', 'like', $search);
                        });
                    });
                })
                ->latest()
                ->paginate(10)
        ]);
    }
}
