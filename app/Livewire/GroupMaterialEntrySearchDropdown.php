<?php

namespace App\Livewire;

use App\Models\Material;
use Livewire\Component;

class GroupMaterialEntrySearchDropdown extends Component
{
    public $search = '';

    public $searchResults = [];

    public $selectedMaterial = '';

    public $selectedMaterialId = 0;

    public function searchResult()
    {
        if (strlen($this->search) < 2) {

            $this->searchResults = [];

            return;
        }
        $materials =
            Material::where('name', 'like', '%'. $this->search .'%')
                ->orderBy('name')
                ->take(30)->get();

        $this->searchResults = $materials;
    }

    public function selectSearch($material_id,$material)
    {
        $this->selectedMaterial = $material;
        $this->selectedMaterialId = $material_id;
        $this->searchResults = [];
        $this->search = $material;
    }

    public function clear()
    {
        $this->search = $this->selectedName;
    }
    public function render()
    {
        return view('livewire.group-material-entry-search-dropdown');
    }
}
