<?php

namespace App\Livewire;

use App\Models\ImportMaterialStock;
use App\Models\MaterialIssuanceItem;
use Livewire\Component;

class MaterialTakeModal extends Component
{
    public $show = false;
    public $currentIndex = null;
    public $selectedMaterial = null;
    public $selectedMaterialId = null;
    public $takeQty = 0;
    public $takeFactQty = 0;
    public $materials = [];
    public $materialIssuanceId = null;
    public $detail_name = null;
    public $material_name = null;
    public $editingId = null;
    public $type = null;
    public $designationId = null;
    public $purchaseId = null;

    protected $listeners = [
        'openMaterialModal' => 'open',
        'openEditMaterialModal' => 'openEdit',
        'materialSelected' => 'setMaterial',
    ];

    public function setMaterial($id)
    {
        $this->selectedMaterialId = $id;
    }

    public function open($currentIndex,$detail_name, $material_name,$materialIssuanceId,$norm, $type = null, $designationId = null, $purchaseId = null)
    {
        $this->resetValidation();
        $this->editingId = null;
        $this->takeFactQty = 0;
        $this->type = $type;
        $this->designationId = $designationId;
        $this->purchaseId = $purchaseId;
        $this->currentIndex = $currentIndex;
        $this->detail_name = $detail_name;
        $this->material_name = $material_name;
        $this->materialIssuanceId = $materialIssuanceId;
        $this->show = true;
        $this->selectedMaterial = null;
        $this->selectedMaterialId = null;
        $this->takeQty = $norm;
    }

    public function openEdit($currentIndex,$detail_name, $material_name,$materialIssuanceId, $type = null, $designationId = null, $purchaseId = null)
    {
        $this->type = $type;
        $this->designationId = $designationId;
        $this->purchaseId = $purchaseId;
        $this->currentIndex = $currentIndex;
        $this->materialIssuanceId = $materialIssuanceId;
        $this->detail_name = $detail_name;
        $this->material_name = $material_name;
        if ($this->type === 'purchase') {
            $item = MaterialIssuanceItem::where('material_issuance_id', $this->materialIssuanceId)
                ->where('designation_id', $this->designationId)->where('purchase_id', $this->purchaseId)->firstOrFail();
        } elseif (is_numeric($this->currentIndex)) {
            $item = MaterialIssuanceItem::where('material_id',$this->currentIndex)->where('material_issuance_id',$this->materialIssuanceId)->first();

        } else {
            $item = MaterialIssuanceItem::where('designation_id', (int) $this->currentIndex)->whereNull('purchase_id')->where('material_issuance_id',$this->materialIssuanceId)->firstOrFail();

        }
        $this->editingId = $item->id;
        $this->selectedMaterial = $item->importMaterial?->name ?? '—';
        $this->selectedMaterialId = $item->import_material_id;
        $this->takeQty = $item->quantity;
        $this->takeFactQty = $item->fact_quantity;
        $this->show = true;
    }

    public function save()
    {
        if ($this->type === 'purchase') {
            $this->validate([
                'designationId' => ['required', 'integer', 'exists:designations,id'],
                'purchaseId' => ['required', 'integer', \Illuminate\Validation\Rule::exists('purchases', 'id')
                    ->where('designation_entry_id', $this->designationId)],
            ]);
        }
        $data = [
            'material_issuance_id' => $this->materialIssuanceId,
            'import_material_id' => $this->selectedMaterialId,
            'details' => $this->detail_name,
            'quantity' => $this->takeQty,
            'fact_quantity' => $this->takeFactQty,
            'material_id' => null,
            'designation_id' => null,
            'purchase_id' => null,
        ];

        if ($this->type === 'purchase') {
            $data['designation_id'] = $this->designationId;
            $data['purchase_id'] = $this->purchaseId;
        } elseif (is_numeric($this->currentIndex)) {
            $data['material_id'] = (int) $this->currentIndex;
        } else {
            //dd($this->currentIndex,(int) $this->currentIndex);
            $data['designation_id'] = (int) $this->currentIndex;
        }
       // dd($this->currentIndex,$data);
        if ($this->editingId) {
            MaterialIssuanceItem::where('id', $this->editingId)->update($data);
        } else {
            MaterialIssuanceItem::create($data);
        }
        $this->editingId = null;
        // 🔥 повідомляємо батьківський компонент
        $this->dispatch('materialUpdated');

        $this->show = false;
    }

    public function render()
    {
        return view('livewire.material-take-modal');
    }
}
