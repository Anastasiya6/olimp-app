<?php

namespace App\Livewire;

use App\Models\Material;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class GroupMaterialSearchDropdown extends Component
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
                ->take(6)->get();

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
        return view('livewire.group-material-search-dropdown');
    }
}
