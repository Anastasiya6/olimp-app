<?php

namespace App\Livewire;

use App\Models\Designation;
use App\Models\PlanTask;
use App\Models\Specification;
use App\Models\TypeUnit;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class PlanTaskSearchDropdown extends Component
{
    public $search = '';

    public $searchResults = [];

    public $selectedDesignation = '';

    public $selectedDesignationId = '';

    public $selectedName = '';

    public $selectedOrder;

    public $sender_department_id;

    public $receiver_department_id;

    public $search_designation_message;

    public $type = 0;

    public function mount($selectedOrder,$sender_department_id,$receiver_department_id)
    {

        $this->message = '';

        $this->selectedOrder = $selectedOrder;

        $this->sender_department_id = $sender_department_id;

        $this->receiver_department_id = $receiver_department_id;
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

        $count = 1;

        if($designations->count() == 1 && $designations[0]->designation !== $this->search){

            $count = 0;
        }

        if(count($designations) == 0 || $count == 0){

            $this->searchResults = [];

            $this->selectedDesignation = '';

        }elseif(count($designations)==1){


            $this->searchResults = [];

            $this->selectedDesignation = $this->search;

            $this->selectedDesignationId = $designations->first()->id;

            if($this->search_in_plan()){

                $this->search_designation_message = 'Така деталь вже є в плані';

            }else{

                $this->search_designation_message = '';
            }

        }else{

            $this->searchResults = $designations;

        }
    }

    public function selectSearch($designation,$name,$id)
    {
        $this->selectedDesignationId = $id;
        $this->selectedDesignation = $designation;
        $this->selectedName = $name;
        $this->searchResults = [];
        $this->search = $name;
        if($this->search_in_plan()){
            $this->search_designation_message = 'Така деталь вже є в плані';
        }else{
            $this->search_designation_message = '';
        }
    }

    public function search_in_plan()
    {
        return PLanTask
            ::where('designation_id',$this->selectedDesignationId)
            ->where('order_name_id',$this->selectedOrder)
            ->where('sender_department_id',$this->sender_department_id)
            ->where('receiver_department_id',$this->receiver_department_id)
            ->first();

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
