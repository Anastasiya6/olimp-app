<?php

namespace App\Livewire;

use App\Models\DesignationMaterial;
use Livewire\Component;

class DesignationMaterialSearch extends Component
{
    public $searchTerm;

    public function render()
    {
        $searchTerm = '%' . $this->searchTerm . '%';
        $items = DesignationMaterial::whereHas('designation', function ($query) use ($searchTerm) {
            $query->where('designation', 'like', "%$searchTerm%");
        })
            ->orderBy('updated_at','desc')
            ->paginate(50);
        $route = 'designation-materials';
        return view('livewire.designation-material-search',compact('items','route'));
    }
}
