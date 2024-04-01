<?php

namespace App\Livewire;

use App\Models\Designation;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SpecificationSearchDropdown extends Component
{
    public $search = 'ААМВ';

    public $searchResults = [];

    public $selectedDesignation = '';

    public $selectedName = '';

    public $newDesignation = false;

    public $newDesignationRoute = false;

    public $newDesignationGost = false;

    public $type = 0;

    public function mount()
    {
        $this->search = 'ААМВ';
    }

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
        $this->newDesignationRoute = false;
        $this->newDesignationGost = false;

        if(count($designations)==0){

            $this->selectedDesignation = $this->search;

            $this->newDesignation = true;

            if (strpos($this->search, 'ПИ0') === 0) {

                $this->newDesignationGost = true;

                $this->type = 1;

            } else {

                $this->newDesignationRoute = true;
            }
        }
    }

    public function selectSearch($designation,$name)
    {
        Log::info('selectSearch');
        $this->selectedDesignation = $designation;
        $this->selectedName = $name;
        $this->searchResults = [];
        $this->search = $name;
    }

    public function clear()
    {
        $this->search = $this->selectedName;
    }

    public function render()
    {
        return view('livewire.specification-search-dropdown');
    }
}
