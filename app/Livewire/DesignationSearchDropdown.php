<?php

namespace App\Livewire;

use App\Models\Designation;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class DesignationSearchDropdown extends Component
{
    public $search = '';

    public $searchResults = [];

    public $selectedDesignation = '';

    public $selectedName = '';

    public $newDesignation = false;

    public $selectedDesignationId = 0;

    public $designation_name = 'designation';

    public $designation_title = 'designation';

    public $designation_hidden = 'designation_id';

    public function mount($designation_hidden,$designation_name,$designation_title)
    {
        $this->designation_hidden = $designation_hidden;

        $this->designation_name = $designation_name;

        $this->designation_title = $designation_title;
    }

    public function searchResult()
    {
        if (strlen($this->search) < 2) {

            $this->searchResults = [];

            return;
        }

        $designations =
            Designation::where('designation', 'like', '%'. trim($this->search) .'%')
                ->orderBy('designation')
                ->take(30)->get();

        $this->searchResults = $designations;
        $this->newDesignation = false;

    }

    public function selectSearch($designation_id,$designation,$name)
    {
        $this->selectedDesignation = $designation;
        $this->selectedName = $name;
        $this->selectedDesignationId = $designation_id;
        $this->searchResults = [];
        $this->search = $name;
    }

    public function render()
    {
        return view('livewire.designation-search-dropdown');
    }
}
