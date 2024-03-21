<?php

namespace App\Livewire;

use App\Models\Designation;
use Livewire\Component;
use Livewire\WithPagination;

class DesignationSearch extends Component
{
    use WithPagination;

    public $searchTerm;

    public function render()
    {
        $searchTerm = '%' . $this->searchTerm . '%';
        $items = Designation::where('name', 'like', $searchTerm)->orderBy('updated_at','desc')
            ->paginate(50);
        $route = 'designations';
        return view('livewire.designation-search',compact('items','route'));
    }
}
