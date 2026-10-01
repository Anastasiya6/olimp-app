<?php

namespace App\Livewire;

use App\Models\DesignationMaterial;
use App\Models\GroupMaterial;
use App\Models\Material;
use App\Models\TypeUnit;
use Livewire\Component;
use Livewire\WithPagination;

class MaterialSearch extends Component
{
    use WithPagination;

    public $searchTerm;

    public $material_message;

    public ?Material $editingMaterial = null;

    public int $editFormVersion = 0;

    public function editMaterial($id)
    {
        $this->editingMaterial = Material::findOrFail($id);
        $this->editFormVersion++;
        $this->resetValidation();
        $this->dispatch('material-edit-open');
    }

    public function deleteMaterial($id)
    {
        $this->material_message = '';
        $material = Material::findOrFail($id);

        $searchInDesignationMaterial = DesignationMaterial::where('material_id',$material->id)->first();

        $searchInGroupMaterial = GroupMaterial::where('material_id',$material->id)->first();

        if(isset($searchInDesignationMaterial->id) || isset($searchInGroupMaterial->id)){
            $this->material_message = "Матеріал використовується у деталях або матеріалокомплектах. Видалити його неможливо.";
            return false;
        }else{
            $material->delete();
            if ($this->editingMaterial?->id === $material->id) {
                $this->editingMaterial = null;
            }
            return true;
        }

    }
    protected function materials()
    {
        $searchTerm = '%' . trim($this->searchTerm) . '%';
        return Material::where('name', 'like', $searchTerm)->orderBy('updated_at','desc')
            ->orWhere('code', 'like', '%' . $searchTerm . '%')
            ->with('unit')
            ->paginate(15);
    }
    public function render()
    {
        return view('livewire.material-search',[
            'items' => $this->materials(),
            'units' => TypeUnit::all(),
            'route' => 'materials'
        ]);
    }
}
