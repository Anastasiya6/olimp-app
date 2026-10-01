<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class EmployeeIndex extends Component
{
    public ?User $editingEmployee = null;

    public int $editFormVersion = 0;

    public function mount()
    {
        if ((old('_employee_create') || old('_employee_edit')) && session()->has('errors')) {
            $this->setErrorBag(session('errors')->getBag('default'));
        }
        if (old('_employee_edit')) {
            $this->editingEmployee = User::find(old('_employee_edit'));
        }
    }

    public function editEmployee($id)
    {
        $this->editingEmployee = User::findOrFail($id);
        $this->editFormVersion++;
        $this->resetValidation();
        $this->dispatch('employee-edit-open');
    }

    public function render()
    {
        return view('livewire.employee-index', ['items' => User::all()]);
    }
}
