<?php

namespace App\Livewire;

use App\Models\Designation;
use App\Models\Specification;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SpecificationDesignationSearch extends Component
{
    public $searchWhere = '';

    public $selectedDesignation = '';

    public $newDesignationWhere = false;

    public function mount()
    {
        $last = Specification::orderBy('id','desc')->with('designations')->first();

        $this->searchWhere = old('_specification_create') ? old('designation_designation', '') : ($last?->designation ?? '');
        if (old('_specification_create')) {
            $this->searchWhereResult();
        }
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

        $this->newDesignationWhere = false;

        if(count($designations)==0){

            $this->newDesignationWhere = true;
        }
    }

    public function render()
    {
        return view('livewire.specification-designation-search');
    }
}
