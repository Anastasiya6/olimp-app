<?php

namespace App\Livewire;

use App\Models\Specification;
use Livewire\Component;
use Livewire\WithPagination;

class SpecificationSearch extends Component
{
    use WithPagination;

    public $searchTerm;

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function render()
    {
        $searchTerm = '%' . $this->searchTerm . '%';
        $specifications = Specification::whereHas('designations', function ($query) use ($searchTerm) {
                                $query->where('designation', 'like', "%$searchTerm%");
                            })
                                ->orderBy('designation')
                                ->paginate(50);
        return view('livewire.specification-search',compact('specifications'));
    }
}
