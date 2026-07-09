<?php

namespace App\Livewire;

use App\Models\Designation;
use App\Models\MaterialIssuance;
use App\Models\MaterialIssuanceItem;
use App\Models\OrderName;
use App\Models\User;
use App\Services\HelpService\MaterialService;
use App\Services\HelpService\PlanService;
use Livewire\Component;

class IssuanceMaterialPage extends Component
{
    public $route = 'issuance-materials';

    public $generated = false;

    public $order_name_id;

    public $designation_id;

    public $quantity;

    public $all_materials;

    public $materialIssuanceId = null;

    public $currentIndex = null;

    public $planDesignationName=null;

    public array $selectedMaterials =  [
        'material_id' => [],
        'designation_id' => [],
    ];

    public array $selectedBalances = [];

    public $designationName = null;

    public $isEdit = false;

    public $users = [];

    public $received_by_user_id;

    public $issued_by_user_id;


    protected $listeners = [
        'materialUpdated' => 'loadSelectedMaterials',
        'designationSelected' => 'designationSelected',
    ];

    public function mount($id = null)
    {
        $this->users = User::orderBy('name')->get();
        if ($id) {
            $this->isEdit = true;

            $issuance = MaterialIssuance::findOrFail($id);
            $this->designationName = $issuance->designation->designation;
            $this->materialIssuanceId = $issuance->id;
            $this->order_name_id = $issuance->order_name_id;
            $this->designation_id = $issuance->designation_id;
            $this->quantity = $issuance->quantity;
            $this->received_by_user_id = $issuance->received_by_user_id;
            $this->issued_by_user_id = $issuance->issued_by_user_id;

            $materialService = app(MaterialService::class);
            $this->all_materials = $materialService->material(collect([$issuance]),1,5,'material_id');

            $this->generated = true;
            $this->planDesignationName = $issuance->planTaskDesignation?->designation;
            $this->loadSelectedMaterials();
        }
    }

    public function loadPlanDesignation(){

        $this->planDesignationName = null;

        if (
            $this->designation_id &&
            $this->order_name_id &&
            $detail_from_plan = PlanService::getDetailFromPlan(
                $this->designation_id,
                $this->order_name_id
            )
        ) {
            $this->planDesignationName = $detail_from_plan->designation->designation;
        }
    }

    public function designationSelected($value)
    {
        $this->designation_id = $value;
        $this->loadPlanDesignation();
    }

    public function updateSearch()
    {
        $this->loadPlanDesignation();
    }

    public function openModal($material_id, $detail_name, $material_name)
    {
        $this->dispatch('openMaterialModal', $material_id, $detail_name, $material_name, $this->materialIssuanceId);
    }

    public function openEditModal($material_id, $detail_name, $material_name)
    {
        $this->dispatch('openEditMaterialModal', $material_id, $detail_name, $material_name, $this->materialIssuanceId);
    }

    public function removeMaterial($materialId)
    {
        $query = MaterialIssuanceItem::where('material_issuance_id', $this->materialIssuanceId);

        if (is_numeric($materialId)) {
            // це material
            $query->where('material_id', $materialId);
        } else {
            // це designation → чистимо id
            $query->where('designation_id', (int) $materialId);
        }

        $query->delete();

        $this->loadSelectedMaterials();
    }

    public function generate(MaterialService $materialService)
    {
        $this->validate([
            'received_by_user_id' => 'required|exists:users,id',
            'issued_by_user_id' => 'required|exists:users,id',
            'order_name_id' => 'required',
            'designation_id' => 'required',
            'quantity' => 'required|numeric|min:1',
        ]);
        $plan_task_designation_id = null;
        if($detail_from_plan = PlanService::getDetailFromPlan($this->designation_id,$this->order_name_id)){
            $plan_task_designation_id = $detail_from_plan->designation_id;
        }

        $materialIssuance = MaterialIssuance::create([
            'order_name_id' => $this->order_name_id,
            'designation_id' => $this->designation_id,
            'quantity' => $this->quantity,
            'received_by_user_id' => $this->received_by_user_id,
            'issued_by_user_id' => $this->issued_by_user_id,
            'plan_task_designation_id' => $plan_task_designation_id
        ]);
        $this->materialIssuanceId = $materialIssuance->id;

        $this->all_materials = $materialService->material(collect([$materialIssuance]),1,5,'material_id');
       // dd($this->all_materials);
        $this->generated = true;

    }

    public function updatedDesignationId()
    {
        $this->loadPlanDesignation();
    }

    public function loadSelectedMaterials()
    {
        $items = MaterialIssuanceItem::where('material_issuance_id', $this->materialIssuanceId)->get();

        $result = [
            'material_id' => [],
            'designation_id' => [],
        ];

        foreach ($items as $item) {
            if ($item->designation_id) {

                $result['designation_id'][$item->designation_id] = $item->quantity;
            }elseif ($item->material_id) {

                $result['material_id'][$item->material_id] = $item->quantity;
            }
        }
        $this->selectedMaterials = $result;
    }

    public function closeDocument()
    {
        return redirect()->route('issuance-materials.index');
    }

    public function render()
    {
        return view('livewire.issuance-material-page',[
            'order_names' => OrderName::where('is_order',1)->orderBy('name')->get(),
            'materials' => $this->all_materials
        ]);
    }
}
