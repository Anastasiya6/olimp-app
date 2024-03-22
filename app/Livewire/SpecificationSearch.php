<?php

namespace App\Livewire;

use App\Models\Specification;
use Livewire\Component;
use Livewire\WithPagination;

class SpecificationSearch extends Component
{
    use WithPagination;

    public $searchTerm;

    public $searchTermChto;

    public function render()
    {
        $searchTerm = '%' . $this->searchTerm . '%';
        $searchTermChto = '%' . $this->searchTermChto . '%';
        $specifications = Specification::whereHas('designations', function ($query) use ($searchTerm) {
            $query->where('designation', 'like', $searchTerm);
        })
            ->whereHas('designationEntry', function ($query) use ($searchTermChto) {
                $query->where('designation', 'like', $searchTermChto);
            })
            ->orderByRaw("CAST(designation AS SIGNED)")
            ->paginate(50);

        return view('livewire.specification-search',compact('specifications'));
    }
}
