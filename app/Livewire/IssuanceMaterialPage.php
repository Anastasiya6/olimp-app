<?php

namespace App\Livewire;

use App\Models\Designation;
use App\Models\MaterialIssuance;
use App\Models\MaterialIssuanceItem;
use App\Models\OrderName;
use App\Services\HelpService\MaterialService;
use Livewire\Component;

class IssuanceMaterialPage extends Component
{
    public $route = 'issuance-materials';

    public $generated = false;

    public $order_name_id;

    public $designation_id;

    public $quantity;

    public string $issued_to_employee;

    public string $issued_by_employee;

    public $all_materials;

    public ?int $materialIssuanceId = null;

    public ?int $currentIndex = null;

    public array $selectedMaterials = [];

    public array $selectedBalances = [];

    protected $listeners = [
        'materialUpdated' => 'loadSelectedMaterials',
        'designationSelected' => 'designationSelected',
    ];

    public function designationSelected($value)
    {
        $this->designation_id = $value;
    }

    public function openModal($material_id)
    {
        $this->dispatch('openMaterialModal', $material_id,$this->materialIssuanceId);
    }

    public function removeMaterial($materialId)
    {
        MaterialIssuanceItem::where('material_issuance_id', $this->materialIssuanceId)
            ->where('material_id', $materialId)
            ->delete();

        $this->loadSelectedMaterials();
    }

    public function generate(MaterialService $materialService)
    {
        $this->validate([
            'issued_to_employee' => 'required',
            'issued_by_employee' => 'required',
            'order_name_id' => 'required',
            'designation_id' => 'required',
            'quantity' => 'required|numeric|min:1',
        ]);
        //$this->designation_id = Designation::where('is_order',1)->orderBy('name')->get()
        $materialIssuance = MaterialIssuance::create([
            'order_name_id' => $this->order_name_id,
            'designation_id' => $this->designation_id,
            'quantity' => $this->quantity,
            'issued_to_employee' => $this->issued_to_employee,
            'issued_by_employee' => $this->issued_by_employee,
        ]);
        $this->materialIssuanceId = $materialIssuance->id;

        $this->all_materials = $materialService->material(collect([$materialIssuance]),1,5,'material_id');

        $this->generated = true;

    }

    public function loadSelectedMaterials()
    {
        $this->selectedMaterials = MaterialIssuanceItem::where('material_issuance_id', $this->materialIssuanceId)
            ->selectRaw('material_id, SUM(quantity) as total')
            ->groupBy('material_id')
            ->pluck('total', 'material_id')
            ->toArray();
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
