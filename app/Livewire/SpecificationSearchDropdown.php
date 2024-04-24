<?php

namespace App\Livewire;

use App\Models\Designation;
use App\Models\Specification;
use App\Models\TypeUnit;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SpecificationSearchDropdown extends Component
{
    public $search = '';

    public $searchResults = [];

    public $selectedDesignation = '';

    public $selectedName = '';

    public $newDesignation = false;

    public $newDesignationRoute = false;

    public $newDesignationGost = false;

    public $type = 0;

    public function mount()
    {
        $last = Specification::orderBy('updated_at','desc')->with('designations')->first();

        $designation = $last->designationEntry->designation;

        preg_match('/^[^\d]+/', $designation, $matches);

        if(isset($matches[0])){

            $this->search  = $matches[0];

        }
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

        $this->newDesignationRoute = false;

        $this->newDesignationGost = false;

        $count = 1;

        if($designations->count() == 1 && $designations[0]->designation !== $this->search){

            $count = 0;
        }

        if(count($designations) == 0 || $count == 0){

            $this->searchResults = [];

            $this->selectedDesignation = $this->search;

            $this->newDesignation = true;

            if (strpos($this->search, 'ПИ0') === 0) {

                $this->newDesignationGost = true;

                $this->type = 1;

            } else {

                $this->newDesignationRoute = true;
            }
        }elseif(count($designations)==1){

            $this->searchResults = [];

            $this->selectedDesignation = $this->search;

        }else{

            $this->searchResults = $designations;

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
        $units = TypeUnit::all();

        return view('livewire.specification-search-dropdown',compact('units'));
    }
}
