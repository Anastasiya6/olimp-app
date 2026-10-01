<?php

namespace App\Livewire;

use App\Models\MaterialCoefficient;
use Livewire\Component;

class MaterialCoefficientIndex extends Component
{
    public ?MaterialCoefficient $editingMaterialCoefficient = null;

    public int $editFormVersion = 0;

    public function mount()
    {
        if ((old('_materialCoefficient_create') || old('_materialCoefficient_edit')) && session()->has('errors')) {
            $this->setErrorBag(session('errors')->getBag('default'));
        }
        if (old('_materialCoefficient_edit')) {
            $this->editingMaterialCoefficient = MaterialCoefficient::find(old('_materialCoefficient_edit'));
        }
    }

    public function editMaterialCoefficient($id)
    {
        $this->editingMaterialCoefficient = MaterialCoefficient::findOrFail($id);
        $this->editFormVersion++;
        $this->resetValidation();
        $this->dispatch('material-coefficient-edit-open');
    }

    public function render()
    {
        return view('livewire.material-coefficient-index', ['items' => MaterialCoefficient::all()]);
    }
}
