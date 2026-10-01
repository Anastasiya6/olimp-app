<?php

namespace App\Livewire;

use App\Models\DesignationMaterial;
use App\Models\Department;
use Livewire\Component;
use Livewire\WithPagination;

class DesignationMaterialSearch extends Component
{
    use withPagination;

    public $searchTerm;

    public $searchTermMaterial;

    public ?DesignationMaterial $editingNorm = null;

    public int $editFormVersion = 0;

    public function mount()
    {
        if ((old('_norm_create') || old('_norm_edit')) && session()->has('errors')) {
            $this->setErrorBag(session('errors')->getBag('default'));
        }
        if (old('_norm_edit')) {
            $this->editingNorm = DesignationMaterial::with('designation', 'material')->find(old('_norm_edit'));
        }
    }

    public function editNorm($id)
    {
        $this->editingNorm = DesignationMaterial::with('designation', 'material')->findOrFail($id);
        $this->editFormVersion++;
        $this->resetValidation();
        $this->dispatch('norm-edit-open');
    }

    public function deleteDesignationMaterial($id)
    {
        $designationMaterial = DesignationMaterial::findOrFail($id);
        $designationMaterial->delete();
        if ($this->editingNorm?->id === $designationMaterial->id) {
            $this->editingNorm = null;
        }

        // Отправить сообщение об успешном удалении
        session()->flash('message', 'Запис успішно видалено.');
    }

    protected function designationMaterials()
    {
        $searchTerm = '%' . trim($this->searchTerm) . '%';

        $searchTermMaterial = '%' . trim($this->searchTermMaterial) . '%';

        if($searchTerm!='%%' || $searchTermMaterial!='%%'){

            return DesignationMaterial::whereHas('designation', function ($query) use ($searchTerm) {
                $query->where('designation', 'like', "%$searchTerm%");
            })
                ->whereHas('material', function ($query) use ($searchTermMaterial) {
                    $query->where('name', 'like', "%$searchTermMaterial%");
                })
                ->orderBy('updated_at','desc')
                ->with('designation','material','department')
                ->paginate(50);

        }else{

            return DesignationMaterial
                ::orderBy('updated_at','desc')
                ->with('designation','material','department')
                ->paginate(50);
        }
    }

    public function render()
    {
        return view('livewire.designation-material-search',[
            'items' => $this->designationMaterials(),
            'departments' => Department::all(),
            'default_department' => Department::DEFAULT_DEPARTMENT,
            'route' => 'designation-materials']);
    }
}
