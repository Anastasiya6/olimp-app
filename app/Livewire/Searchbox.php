<?php

namespace App\Livewire;

use App\Models\Designation;
use Livewire\Component;

class Searchbox extends Component
{
    public $showdiv = false;
    /** @modelable  */
    public $search = "";
    public $records;
    public $showresult = true;
    public $name = "";
    public $selectedDesignation = '';

    public $selectedName = '';
    // Fetch records
    public function searchResult(){

        if(!empty($this->search)){

            $this->records = Designation::orderby('name','asc')
                ->select('*')
                ->where('name','like','%'.$this->search.'%')
                ->limit(5)
                ->get();

            $this->showdiv = true;
        }else{
            $this->showdiv = false;
        }
    }

    // Fetch record by ID
    public function fetchEmployeeDetail($id = 0){

        $record = Designation::select('*')
            ->where('id',$id)
            ->first();
        $this->name = $record->name;
        $this->search = $record->name;
        $this->empDetails = $record;
        $this->selectedDesignation = $record->designation;
        $this->selectedName = $record->name;
        $this->showdiv = false;
        $this->showresult = false;

    }

    public function render(){
        return view('livewire.searchbox');
    }
}
