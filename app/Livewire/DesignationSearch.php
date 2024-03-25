<?php

namespace App\Livewire;

use App\Models\Designation;
use Livewire\Component;
use Livewire\WithPagination;

class DesignationSearch extends Component
{
    use WithPagination;

    public $searchTerm;

    public $searchTermChto;

    public function render()
    {
        $searchTerm = '%' . $this->searchTerm . '%';
        $searchTermChto = '%' . $this->searchTermChto . '%';
        $items = Designation::where(function ($query) use ($searchTerm, $searchTermChto) {
            $query->where('name', 'like', $searchTerm)
                ->where('designation', 'like', $searchTermChto)
            ->orderByRaw("CAST(designation AS SIGNED)");
        })
            ->paginate(50);

        $route = 'designations';
        return view('livewire.designation-search',compact('items','route'));
    }
}
