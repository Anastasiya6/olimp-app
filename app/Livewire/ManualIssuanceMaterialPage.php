<?php

namespace App\Livewire;

use App\Models\MaterialIssuance;
use App\Models\MaterialIssuanceItem;
use App\Models\ImportMaterial;
use App\Models\OrderName;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ManualIssuanceMaterialPage extends Component
{
    public bool $inModal = false;

    public string $materialSearch = '';
    public array $materialSearchResults = [];
    public array $issuanceItems = [];
    public string $materialSearchMessage = '';
    public $users = [];
    public $received_by_user_id;

    public $issued_by_user_id;

    public $order_name_id;

    public function mount()
    {
        $this->users = User::orderBy('name')->get();

        $lastOrderId = MaterialIssuance::manual()
            ->whereNull('designation_id')
            ->latest('id')
            ->value('order_name_id');
        if ($lastOrderId && OrderName::where('is_order', 1)->whereKey($lastOrderId)->exists()) {
            $this->order_name_id = $lastOrderId;
        }
    }

    public function updatedMaterialSearch(): void
    {
        $search = trim($this->materialSearch);
        $this->materialSearchMessage = '';

        if (mb_strlen($search) < 2) {
            $this->materialSearchResults = [];
            return;
        }

        $this->materialSearchResults = ImportMaterial::withSum('stocks', 'amount')
            ->where('name', 'like', '%'.$search.'%')
            ->orderBy('name')
            ->limit(50)
            ->get()
            ->map(fn (ImportMaterial $material) => [
                'id' => $material->id,
                'name' => $material->name,
                'article' => $material->article,
                'balance' => $material->stocks_sum_amount ?? 0,
            ])
            ->all();
    }

    public function addMaterial(int $id): void
    {
        $material = ImportMaterial::withSum('stocks', 'amount')->findOrFail($id);

        if (collect($this->issuanceItems)->contains(fn ($item) => (int) $item['import_material_id'] === $material->id)) {
            $this->materialSearchMessage = 'Цей матеріал уже доданий до документа.';
            $this->materialSearch = '';
            $this->materialSearchResults = [];
            return;
        }

        $this->issuanceItems[] = [
            'import_material_id' => $material->id,
            'name' => $material->name,
            'article' => $material->article,
            'balance' => $material->stocks_sum_amount ?? 0,
            'quantity' => '',
        ];
        $this->materialSearch = '';
        $this->materialSearchResults = [];
        $this->materialSearchMessage = '';
        $this->resetValidation('issuanceItems');
    }

    public function removeMaterial(int $index): void
    {
        unset($this->issuanceItems[$index]);
        $this->issuanceItems = array_values($this->issuanceItems);
    }

    public function save()
    {
        $this->validate([
            'received_by_user_id' => 'required|exists:users,id',
            'issued_by_user_id' => 'required|exists:users,id',
            'order_name_id' => 'required|exists:order_names,id',
            'issuanceItems' => 'required|array|min:1',
            'issuanceItems.*.import_material_id' => 'required|distinct|exists:import_materials,id',
            'issuanceItems.*.quantity' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () {

            $issuance = MaterialIssuance::create([
                'received_by_user_id' => $this->received_by_user_id,
                'issued_by_user_id' => $this->issued_by_user_id,
                'order_name_id' => $this->order_name_id ?: null,
                'quantity' => 0
            ]);

            foreach ($this->issuanceItems as $item) {
                MaterialIssuanceItem::create([
                    'material_issuance_id' => $issuance->id,
                    'import_material_id' => $item['import_material_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

        });

        return redirect()->route('manual-issuance-materials.index');
    }

    public function render()
    {
        return view('livewire.manual-issuance-material-page',[
            'order_names' => OrderName::where('is_order', 1)->orderBy('name')->get(),
        ]);
    }
}
