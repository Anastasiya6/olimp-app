<?php

namespace App\Livewire;

use App\Models\OrderName;
use Livewire\Component;
use Livewire\WithPagination;

class OrderNameIndex extends Component
{
    use WithPagination;

    public string $searchTerm = '';

    public ?OrderName $editingOrderName = null;

    public int $editFormVersion = 0;

    public function mount()
    {
        if ((old('_order_name_create') || old('_order_name_edit')) && session()->has('errors')) {
            $this->setErrorBag(session('errors')->getBag('default'));
        }
        if (old('_order_name_edit')) {
            $this->editingOrderName = OrderName::find(old('_order_name_edit'));
        }
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function editOrderName($id)
    {
        $this->editingOrderName = OrderName::findOrFail($id);
        $this->editFormVersion++;
        $this->resetValidation();
        $this->dispatch('order-name-edit-open');
    }

    public function deleteOrderName($id)
    {
        OrderName::findOrFail($id)->delete();
        if ($this->editingOrderName?->id === (int) $id) {
            $this->editingOrderName = null;
        }
    }

    public function render()
    {
        return view('livewire.order-name-index', [
            'items' => OrderName::when(trim($this->searchTerm) !== '', fn ($query) => $query->where('name', 'like', '%'.trim($this->searchTerm).'%'))
                ->orderBy('created_at', 'desc')->orderBy('id', 'desc')->paginate(25),
        ]);
    }
}
