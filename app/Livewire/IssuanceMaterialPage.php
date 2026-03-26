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

    public $all_materials;

    public  $materialIssuanceId = null;

    public bool $showModal = false;

    public ?int $currentIndex = null;

    public array $selectedMaterials = [];

    public array $takeQty = [];
    public array $selectedBalances = [];

    protected $listeners = [
        'materialUpdated' => 'loadSelectedMaterials'
    ];

    public function openModal(int $material_id)
    {

        $this->dispatch('openMaterialModal', $material_id,$this->materialIssuanceId);
    }

    public function generate(MaterialService $materialService)
    {
        $this->validate([
            'order_name_id' => 'required',
            //'designation_id' => 'required',
            'quantity' => 'required|numeric|min:1',
        ]);
        //$this->designation_id = Designation::where('is_order',1)->orderBy('name')->get()
        $materialIssuance = MaterialIssuance::create([
            'order_name_id' => $this->order_name_id,
            'designation_id' => $this->designation_id,
            'quantity' => $this->quantity,
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

    public function render()
    {
        return view('livewire.issuance-material-page',[
            'order_names' => OrderName::where('is_order',1)->orderBy('name')->get(),
            'materials' => $this->all_materials
        ]);
    }
}
