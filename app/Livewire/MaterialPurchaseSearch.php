<?php

namespace App\Livewire;

use App\Models\MaterialPurchase;
use App\Models\OrderName;
use Livewire\Component;
use Livewire\WithPagination;

class MaterialPurchaseSearch extends Component
{
    use WithPagination;

    public $route = 'material-purchases';

    public $searchTerm;

    public $searchTermChto;

    public ?MaterialPurchase $editingMaterialPurchase = null;

    public int $editFormVersion = 0;

    public function mount()
    {
        if ((old('_material_purchase_create') || old('_material_purchase_edit')) && session()->has('errors')) {
            $this->setErrorBag(session('errors')->getBag('default'));
        }
        if (old('_material_purchase_edit')) {
            $this->editingMaterialPurchase = MaterialPurchase::with('designation', 'designationEntry', 'order_names', 'material.unit')->find(old('_material_purchase_edit'));
        }
    }

    public function editMaterialPurchase($id)
    {
        $this->editingMaterialPurchase = MaterialPurchase::with('designation', 'designationEntry', 'order_names', 'material.unit')->findOrFail($id);
        $this->editFormVersion++;
        $this->resetValidation();
        $this->dispatch('material-purchase-edit-open');
    }

    public function updatedSearchTerm() { $this->resetPage(); }

    public function updatedSearchTermChto() { $this->resetPage(); }

    public function updateSearch()
    {
        $this->resetPage();
    }

    public function deleteMaterialPurchase($id)
    {
        $purchase = MaterialPurchase::findOrFail($id);
        $purchase->delete();
        if ($this->editingMaterialPurchase?->id === $purchase->id) {
            $this->editingMaterialPurchase = null;
        }

        // Отправить сообщение об успешном удалении
        session()->flash('message', 'Запис успішно видалено.');
    }

    protected function purchases()
    {
        $searchTerm = '%' . trim($this->searchTerm) . '%';

        $searchTermChto = '%' . trim($this->searchTermChto) . '%';

        if ($searchTerm == '%%' && $searchTermChto == '%%') {
            $purchases = MaterialPurchase::with('designation', 'designationEntry', 'order_names', 'material.unit')
                ->orderBy('updated_at', 'desc')
                ->paginate(25);
        } else {
            $purchases = MaterialPurchase::whereHas('designation', function ($query) use ($searchTerm) {
                $query->where('designation', 'like', $searchTerm)
                    ->orderByRaw("CAST(designation AS SIGNED)");
            })
                ->whereHas('designationEntry', function ($query) use ($searchTermChto) {
                    $query->where('designation', 'like', $searchTermChto)
                        ->orderByRaw("CAST(designation AS SIGNED)");
                })
                ->with('designation', 'designationEntry', 'order_names', 'material.unit')
                ->paginate(25);
        }

        return $purchases;
    }

    public function render()
    {
        return view('livewire.material-purchase-search',[
            'items' => $this->purchases(),
            'order_names' => OrderName::where('is_order', 1)->orderBy('name')->get(),
            'route' => $this->route,
        ]);
    }
}
