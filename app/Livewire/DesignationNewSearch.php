<?php

namespace App\Livewire;

use App\Models\Designation;
use App\Models\Specification;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class DesignationNewSearch extends Component
{
    public $searchWhere = '';

    public $selectedDesignation = '';

    public $newDesignationWhere = false;

    public function mount()
    {
        $last = Specification::orderBy('id','desc')->with('designations')->first();

        $this->searchWhere = $last->designation;
    }

    public function searchWhereResult()
    {
        if (strlen($this->searchWhere) < 2) {

            return;
        }

        $designations =
            Designation::where('designation', 'like', '%'. $this->searchWhere .'%')
                ->orderBy('designation')
                ->take(6)->get();

        $this->newDesignation = false;

        if(count($designations)==0){

            $this->newDesignationWhere = true;
        }
    }

    public function render()
    {
        return view('livewire.designation-new-search');
    }
}
