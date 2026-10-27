<?php

namespace App\Livewire;

use App\Models\MaterialIssuance;
use App\Models\MaterialIssuanceItem;
use App\Models\ImportMaterial;
use App\Models\Department;
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

    public $department_id;

    public ?int $documentId = null;

    public bool $isEdit = false;

    public function mount(?int $id = null)
    {
        $this->users = User::orderBy('name')->get();

        if ($id !== null) {
            $issuance = MaterialIssuance::manual()->with('items.importMaterial')->findOrFail($id);
            $this->documentId = $issuance->id;
            $this->isEdit = true;
            $this->order_name_id = $issuance->order_name_id;
            $this->department_id = $issuance->department_id;
            $this->received_by_user_id = $issuance->received_by_user_id;
            $this->issued_by_user_id = $issuance->issued_by_user_id;
            $this->issuanceItems = $issuance->items->map(fn (MaterialIssuanceItem $item) => [
                'import_material_id' => $item->import_material_id,
                'name' => $item->importMaterial?->name ?? '',
                'article' => $item->importMaterial?->article,
                'balance' => $item->importMaterial?->stocks()->sum('amount') ?? 0,
                'quantity' => $item->quantity,
            ])->all();

            return;
        }

        $lastOrderId = MaterialIssuance::manual()
            ->whereNull('designation_id')
            ->latest('id')
            ->first(['order_name_id', 'department_id']);
        if ($lastOrderId) {
            $this->department_id = $lastOrderId->department_id;
        }
        $lastOrderId = $lastOrderId?->order_name_id;
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
            'department_id' => 'nullable|exists:departments,id',
            'issuanceItems' => 'required|array|min:1',
            'issuanceItems.*.import_material_id' => 'required|distinct|exists:import_materials,id',
            'issuanceItems.*.quantity' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () {
            $issuance = $this->isEdit
                ? MaterialIssuance::manual()->lockForUpdate()->findOrFail($this->documentId)
                : new MaterialIssuance();
            abort_if($this->isEdit && $issuance->status === 'posted', 403);

            $issuance->fill([
                'received_by_user_id' => $this->received_by_user_id,
                'issued_by_user_id' => $this->issued_by_user_id,
                'order_name_id' => $this->order_name_id ?: null,
                'department_id' => $this->department_id ?: null,
                'quantity' => 0,
            ]);
            $issuance->save();
            $issuance->items()->delete();

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
            'departments' => Department::orderBy('number')->get(),
        ]);
    }
}
