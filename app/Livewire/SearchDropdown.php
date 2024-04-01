<?php

namespace App\Livewire;

use App\Models\Designation;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SearchDropdown extends Component
{
    public $search = 'ААМВ';

    public $searchResults = [];

    public $selectedDesignation = '';

    public $selectedName = '';

    public $newDesignation = false;

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

        if(count($designations)==0){

            $this->selectedDesignation = $this->search;
            $this->newDesignation = true;
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
        return view('livewire.search-dropdown');
    }
}
