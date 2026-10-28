<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\ImportMaterial;
use App\Models\ImportMaterialStock;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class MaterialReturnIndex extends Component
{
    use WithPagination;

    public string $articleSearch = '';
    public string $quantity = '';
    public string $departmentId = '';
    public ?int $selectedMaterialId = null;

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->reset(['articleSearch', 'quantity', 'selectedMaterialId']);
        $this->departmentId = (string) session('material_return_department_id', '');
        $this->dispatch('material-return-open');
    }

    public function updatedArticleSearch(): void
    {
        $this->selectedMaterialId = null;
    }

    public function selectMaterial(int $id): void
    {
        $material = ImportMaterial::findOrFail($id);
        $this->selectedMaterialId = $material->id;
        $this->articleSearch = (string) $material->article;
    }

    public function save(): void
    {
        $data = $this->validate([
            'selectedMaterialId' => ['required', 'integer', 'exists:import_materials,id'],
            'quantity' => ['required', 'numeric', 'gt:0', 'decimal:0,3', 'max:999999999.999'],
            'departmentId' => ['required', 'integer', 'exists:departments,id'],
        ], [
            'selectedMaterialId.required' => 'Оберіть матеріал зі списку.',
            'quantity.required' => 'Вкажіть кількість повернення.',
            'quantity.numeric' => 'Кількість має бути числом.',
            'quantity.gt' => 'Кількість має бути більшою за нуль.',
            'quantity.decimal' => 'Допускається не більше трьох знаків після коми.',
            'departmentId.required' => 'Оберіть цех, до якого повертається матеріал.',
        ]);

        DB::transaction(function () use ($data) {
            ImportMaterialStock::create([
                'import_material_id' => $data['selectedMaterialId'],
                'amount' => $data['quantity'],
                'type' => 'return',
                'document_date' => now(),
                'department_id' => $data['departmentId'],
            ]);
        });

        session(['material_return_department_id' => $data['departmentId']]);
        $this->resetPage();
        $this->reset(['articleSearch', 'quantity', 'selectedMaterialId']);
        $this->dispatch('material-return-close');
        session()->flash('success', 'Повернення матеріалу успішно додано.');
    }

    public function render()
    {
        $search = trim($this->articleSearch);
        $materialOptions = collect();

        if ($search !== '' && $this->selectedMaterialId === null) {
            $materialOptions = ImportMaterial::query()
                ->with('unit:id,unit')
                ->where('article', 'like', '%'.$search.'%')
                ->orderBy('article')
                ->limit(10)
                ->get(['id', 'article', 'name', 'type_unit_id']);
        }

        $returns = ImportMaterialStock::query()
            ->with(['materials.unit', 'department'])
            ->where('type', 'return')
            ->orderByDesc('document_date')
            ->orderByDesc('id')
            ->paginate(25);

        return view('livewire.material-return-index', [
            'returns' => $returns,
            'materialOptions' => $materialOptions,
            'departments' => Department::orderBy('number')->get(),
            'selectedMaterial' => $this->selectedMaterialId
                ? ImportMaterial::with('unit')->find($this->selectedMaterialId)
                : null,
        ]);
    }
}
