<?php

namespace App\Livewire;

use App\Models\Department;
use Livewire\Component;

class DepartmentIndex extends Component
{
    public ?Department $editingDepartment = null;

    public int $editFormVersion = 0;

    public function mount()
    {
        if ((old('_department_create') || old('_department_edit')) && session()->has('errors')) {
            $this->setErrorBag(session('errors')->getBag('default'));
        }
        if (old('_department_edit')) {
            $this->editingDepartment = Department::find(old('_department_edit'));
        }
    }

    public function editDepartment($id)
    {
        $this->editingDepartment = Department::findOrFail($id);
        $this->editFormVersion++;
        $this->resetValidation();
        $this->dispatch('department-edit-open');
    }

    public function render()
    {
        return view('livewire.department-index', ['items' => Department::all()]);
    }
}
