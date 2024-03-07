<?php

namespace App\Livewire;

use App\Models\Designation;
use Livewire\Component;
use Livewire\WithPagination;

class DesignationSearch extends Component
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
        $designations = Designation::where('name', 'like', $searchTerm)->orderBy('updated_at','desc')
            ->paginate(50);
        return view('livewire.designation-search',compact('designations'));
    }
}
