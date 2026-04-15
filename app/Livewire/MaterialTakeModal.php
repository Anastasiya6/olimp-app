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
    public $materials = [];
    public $materialIssuanceId = null;
    public $detail_name = null;
    public $material_name = null;

    protected $listeners = ['openMaterialModal' => 'open','materialSelected' => 'setMaterial'];

    public function setMaterial($id)
    {
        $this->selectedMaterialId = $id;
    }

    public function open($currentIndex,$detail_name, $material_name,$materialIssuanceId)
    {
        //dd($currentIndex,$materialIssuanceId);
        $this->currentIndex = $currentIndex;
        $this->detail_name = $detail_name;
        $this->material_name = $material_name;
        $this->materialIssuanceId = $materialIssuanceId;
        $this->show = true;
        $this->selectedMaterial = null;
        $this->selectedMaterialId = null;
        $this->takeQty = 0;
    }

    public function save()
    {
        $data = [
            'material_issuance_id' => $this->materialIssuanceId,
            'import_material_id' => $this->selectedMaterialId,
            'details' => $this->detail_name,
            'quantity' => $this->takeQty,
            'material_id' => null,
            'designation_id' => null,
        ];

        if (is_numeric($this->currentIndex)) {
            $data['material_id'] = (int) $this->currentIndex;
        } else {
            //dd($this->currentIndex,(int) $this->currentIndex);
            $data['designation_id'] = (int) $this->currentIndex;
        }

        MaterialIssuanceItem::create($data);
        //dd($this->selectedMaterialId,$this->materialIssuanceId,$this->currentIndex,$this->takeQty);
        //dd($this->currentIndex,$this->materialIssuanceId );
        // Можна робити логіку списання або передавати батьку
//        MaterialIssuanceItem::create([
//            'material_issuance_id' => $this->materialIssuanceId,
//            'material_id' => (int) $this->currentIndex,
//            'import_material_id' => $this->selectedMaterialId,
//            'quantity' => $this->takeQty
//        ]);


//        ImportMaterialStock::create([
//            'import_material_id' => $this->selectedMaterialId,
//            'amount' => -$this->takeQty,
//            'type' => 'stock_out',
//            'document_number' => $this->materialIssuanceId
//        ]);
        // 🔥 повідомляємо батьківський компонент
        $this->dispatch('materialUpdated');

        $this->show = false;
    }

    public function render()
    {
        return view('livewire.material-take-modal');
    }
}
