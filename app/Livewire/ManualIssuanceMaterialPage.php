<?php

namespace App\Livewire;

use App\Models\MaterialIssuance;
use App\Models\MaterialIssuanceItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;

class ManualIssuanceMaterialPage extends Component
{
    public $selectedMaterialId = null;
    public $selectedMaterial = null;
    public $quantity = 0;
    public $users = [];
    public $received_by_user_id;

    public $issued_by_user_id;

    public function mount()
    {
        $this->selectedMaterialId = null;
        $this->selectedMaterial = null;
        $this->quantity = 0;
        $this->users = User::orderBy('name')->get();
    }

    #[On('materialSelected')]
    public function materialSelected($id)
    {
        $this->selectedMaterialId = $id;
    }

    public function save()
    {
        $this->validate([
            'received_by_user_id' => 'required|exists:users,id',
            'issued_by_user_id' => 'required|exists:users,id',
            'selectedMaterialId' => 'required|exists:import_materials,id',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () {

            $issuance = MaterialIssuance::create([
                'received_by_user_id' => $this->received_by_user_id,
                'issued_by_user_id' => $this->issued_by_user_id,
                'quantity' => 0
            ]);

            MaterialIssuanceItem::create([
                'material_issuance_id' => $issuance->id,
                'import_material_id' => $this->selectedMaterialId,
                'quantity' => $this->quantity,
            ]);

        });

        return redirect()->route('manual-issuance-materials.index');
    }

    public function render()
    {
        return view('livewire.manual-issuance-material-page',[
            //'materials' => $this->all_materials
        ]);
    }
}
