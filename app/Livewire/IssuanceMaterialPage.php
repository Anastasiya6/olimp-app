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

    public $materialIssuanceId = null;

    public $currentIndex = null;

    public array $selectedMaterials = [];

    public array $selectedBalances = [];

    public $designationName = null;

    public $isEdit = false;

    protected $listeners = [
        'materialUpdated' => 'loadSelectedMaterials',
        'designationSelected' => 'designationSelected',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $this->isEdit = true;

            $issuance = MaterialIssuance::findOrFail($id);
            $this->designationName = $issuance->designation->designation;
            $this->materialIssuanceId = $issuance->id;
            $this->order_name_id = $issuance->order_name_id;
            $this->designation_id = $issuance->designation_id;
            $this->quantity = $issuance->quantity;
            $this->issued_to_employee = $issuance->issued_to_employee;
            $this->issued_by_employee = $issuance->issued_by_employee;

            $materialService = app(MaterialService::class);
            $this->all_materials = $materialService->material(collect([$issuance]),1,5,'material_id');

            $this->generated = true;

            $this->loadSelectedMaterials();
        }
    }

    public function designationSelected($value)
    {
        $this->designation_id = $value;
    }

    public function openModal($material_id, $detail_name, $material_name)
    {
        $this->dispatch('openMaterialModal', $material_id, $detail_name, $material_name, $this->materialIssuanceId);
    }

    public function removeMaterial($materialId)
    {
        $query = MaterialIssuanceItem::where('material_issuance_id', $this->materialIssuanceId);
        //dd( $materialId);
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
            'issued_to_employee' => 'required',
            'issued_by_employee' => 'required',
            'order_name_id' => 'required',
            'designation_id' => 'required',
            'quantity' => 'required|numeric|min:1',
        ]);

        $materialIssuance = MaterialIssuance::create([
            'order_name_id' => $this->order_name_id,
            'designation_id' => $this->designation_id,
            'quantity' => $this->quantity,
            'issued_to_employee' => $this->issued_to_employee,
            'issued_by_employee' => $this->issued_by_employee,
        ]);
        $this->materialIssuanceId = $materialIssuance->id;

        $this->all_materials = $materialService->material(collect([$materialIssuance]),1,5,'material_id');
       // dd($this->all_materials);
        $this->generated = true;

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
