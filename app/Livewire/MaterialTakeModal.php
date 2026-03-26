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
    public  $materialIssuanceId = null;

    protected $listeners = ['openMaterialModal' => 'open','materialSelected' => 'setMaterial'];

    public function setMaterial($id)
    {
        $this->selectedMaterialId = $id;
    }

    public function open($currentIndex,$materialIssuanceId)
    {
     //   dd($materialId,$materialIssuanceId);
        $this->currentIndex = $currentIndex;
        $this->materialIssuanceId = $materialIssuanceId;
        $this->show = true;
        $this->selectedMaterial = null;
        $this->selectedMaterialId = null;
        $this->takeQty = 0;
    }

    public function save()
    {
        //dd($this->currentIndex,$this->materialIssuanceId );
        // Можна робити логіку списання або передавати батьку
        MaterialIssuanceItem::create([
            'material_issuance_id' => $this->materialIssuanceId,
            'material_id' => $this->currentIndex,
            'import_material_id' => $this->selectedMaterialId,
            'quantity' => $this->takeQty
        ]);


        ImportMaterialStock::create([
            'import_material_id' => $this->selectedMaterialId,
            'amount' => -$this->takeQty,
            'type' => 'stock_out',
            'document_number' => $this->materialIssuanceId
        ]);
        // 🔥 повідомляємо батьківський компонент
        $this->dispatch('materialUpdated');

        $this->show = false;
    }

    public function render()
    {
        return view('livewire.material-take-modal');
    }
}
