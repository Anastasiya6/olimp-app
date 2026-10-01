<?php

namespace App\Livewire;

use App\Models\TypeUnit;
use Livewire\Component;

class TypeUnitIndex extends Component
{
    public ?TypeUnit $editingTypeUnit = null;

    public int $editFormVersion = 0;

    public function mount()
    {
        if ((old('_typeUnit_create') || old('_typeUnit_edit')) && session()->has('errors')) {
            $this->setErrorBag(session('errors')->getBag('default'));
        }
        if (old('_typeUnit_edit')) {
            $this->editingTypeUnit = TypeUnit::find(old('_typeUnit_edit'));
        }
    }

    public function editTypeUnit($id)
    {
        $this->editingTypeUnit = TypeUnit::findOrFail($id);
        $this->editFormVersion++;
        $this->resetValidation();
        $this->dispatch('type-unit-edit-open');
    }

    public function render()
    {
        return view('livewire.type-unit-index', ['items' => TypeUnit::all()]);
    }
}
