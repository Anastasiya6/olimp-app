<?php

namespace App\Livewire;

use App\Models\Designation;
use App\Models\Specification;
use App\Models\TypeUnit;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class PlanTaskSearchDropdown extends Component
{
    public $search = '';

    public $searchResults = [];

    public $selectedDesignation = '';

    public $selectedName = '';

    public $type = 0;

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

        $count = 1;

        if($designations->count() == 1 && $designations[0]->designation !== $this->search){

            $count = 0;
        }

        if(count($designations) == 0 || $count == 0){

            $this->searchResults = [];

            $this->selectedDesignation = '';

            //$this->newDesignation = true;

            /*if (strpos($this->search, 'ПИ0') === 0) {

                $this->newDesignationGost = true;

                $this->type = 1;

            } else {

                $this->newDesignationRoute = true;
            }*/
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

    public function clear()
    {
        $this->search = $this->selectedName;
    }

    public function render()
    {
        $units = TypeUnit::all();

        return view('livewire.plan-task-search-dropdown',compact('units'));
    }
}
