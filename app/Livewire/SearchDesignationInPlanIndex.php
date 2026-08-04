<?php

namespace App\Livewire;

use App\Models\Designation;
use App\Models\OrderName;
use App\Services\HelpService\PlanService;
use Livewire\Component;

class SearchDesignationInPlanIndex extends Component
{
    public $designation_number = '';

    public $selectedOrder = null;

    public $results = [];

    public function search()
    {
        $designation = Designation::where('designation',$this->designation_number)->first();
        if($designation){
            $this->results = PlanService::getDetailFromPlan(
                $designation->id,
                $this->selectedOrder)?? [];
        }else{
            $this->results = [];
        }
    }

    public function render()
    {

        $order_names = OrderName::where('is_order', 1)->orderBy('name')->get();

        if (!$this->selectedOrder && $order_names->count()) {
            $this->selectedOrder = $order_names->first()->id;
        }
        return view('livewire.search-designation-in-plan-index',[
            'order_names'=> $order_names,
        ]);
    }
}
