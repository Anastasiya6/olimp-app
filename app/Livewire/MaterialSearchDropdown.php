<?php

namespace App\Livewire;

use App\Http\Requests\MaterialCreateRequest;
use App\Models\Material;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class MaterialSearchDropdown extends Component
{
    public $search = '';

    public $searchResults = [];

    public $selectedMaterial = '';

    public $selectedMaterialId = 0;

    public function mount($material_id,$material_name)
    {
        if($material_id != null && $material_name != null){

            $this->selectedMaterialId = $material_id;

            $this->selectedMaterial = $material_name;
        }
    }


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
        Log::info('selectSearch');
        Log::info($material);
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
        return view('livewire.material-search-dropdown');
    }
}
