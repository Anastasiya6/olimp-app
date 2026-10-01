<?php

namespace App\Livewire;

use App\Models\Purchase;
use App\Models\OrderName;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseSearch extends Component
{
    use WithPagination;

    public $route = 'purchases';

    public $searchTerm;

    public $searchTermChto;

    public ?Purchase $editingPurchase = null;

    public int $editFormVersion = 0;

    public function mount()
    {
        if ((old('_purchase_create') || old('_purchase_edit')) && session()->has('errors')) {
            $this->setErrorBag(session('errors')->getBag('default'));
        }
        if (old('_purchase_edit')) {
            $this->editingPurchase = Purchase::with('designation', 'designationEntry', 'order_names')->find(old('_purchase_edit'));
        }
    }

    public function editPurchase($id)
    {
        $this->editingPurchase = Purchase::with('designation', 'designationEntry', 'order_names')->findOrFail($id);
        $this->editFormVersion++;
        $this->resetValidation();
        $this->dispatch('purchase-edit-open');
    }

    public function updatedSearchTerm() { $this->resetPage(); }

    public function updatedSearchTermChto() { $this->resetPage(); }

    public function updateSearch()
    {
        $this->resetPage();
    }

    public function deletePurchase($id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->delete();
        if ($this->editingPurchase?->id === $purchase->id) {
            $this->editingPurchase = null;
        }

        // Отправить сообщение об успешном удалении
        session()->flash('message', 'Запис успішно видалено.');
    }

    protected function purchases()
    {
        $searchTerm = '%' . trim($this->searchTerm) . '%';

        $searchTermChto = '%' . trim($this->searchTermChto) . '%';

        if ($searchTerm == '%%' && $searchTermChto == '%%') {
            $purchases = Purchase::with('designation', 'designationEntry', 'order_names')
                ->orderBy('updated_at', 'desc')
                ->paginate(25);
        } else {
            $purchases = Purchase::whereHas('designation', function ($query) use ($searchTerm) {
                $query->where('designation', 'like', $searchTerm)
                    ->orderByRaw("CAST(designation AS SIGNED)");
            })
                ->whereHas('designationEntry', function ($query) use ($searchTermChto) {
                    $query->where('designation', 'like', $searchTermChto)
                        ->orderByRaw("CAST(designation AS SIGNED)");
                })
                ->with('designation', 'designationEntry', 'order_names')
                ->paginate(25);
        }

        return $purchases;
    }

    public function render()
    {
        return view('livewire.purchase-search',[
            'items' => $this->purchases(),
            'order_names' => OrderName::where('is_order', 1)->orderBy('name')->get(),
            'route' => $this->route,
        ]);
    }
}
