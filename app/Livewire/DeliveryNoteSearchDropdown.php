<?php

namespace App\Livewire;

use App\Models\Designation;
use Livewire\Component;

class DeliveryNoteSearchDropdown extends Component
{
    public $search = '';

    public $searchResults = [];

    public $selectedDesignation = '';

    public $selectedName = '';

    public function mount()
    {
        $this->endDate =  \Carbon\Carbon::now()->format('Y-m-d');
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

        $this->newDesignation = false;

        $count = 1;

        if($designations->count() == 1 && $designations[0]->designation !== $this->search){

            $count = 0;
        }

        if(count($designations) == 0 || $count == 0){

            $this->searchResults = [];

            $this->selectedDesignation = $this->search;

            $this->newDesignation = true;

        }elseif(count($designations)==1){

            $this->searchResults = [];

            $this->selectedDesignation = $this->search;

        }else{

            $this->searchResults = $designations;

        }
    }

    public function selectSearch($designation,$name)
    {
        $this->selectedDesignation = $designation;
        $this->selectedName = $name;
        $this->searchResults = [];
        $this->search = $name;
    }

    public function render()
    {
        return view('livewire.delivery-note-search-dropdown');
    }
}
