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


    public function searchResult()
    {
        if (strlen($this->search) < 2) {

            $this->searchResults = [];

            return;
        }

        $designations =
            Designation::where('designation', 'like', '%'. $this->search .'%')
                ->orderBy('designation')
                ->take(6)->get();

        $this->searchResults = $designations;
        $this->newDesignation = false;

    }

    public function selectSearch($designation_id,$designation,$name)
    {
        Log::info('selectSearch');
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
