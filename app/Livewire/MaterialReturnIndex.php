<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\ImportMaterial;
use App\Models\ImportMaterialStock;
use App\Models\MaterialReturn;
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
    public ?int $editingId = null;

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->reset(['articleSearch', 'quantity', 'selectedMaterialId', 'editingId']);
        $this->departmentId = (string) session('material_return_department_id', '');
        $this->dispatch('material-return-open');
    }

    public function updatedArticleSearch(): void
    {
        $this->selectedMaterialId = null;
    }

    public function openEdit(int $id): void
    {
        $document = MaterialReturn::with('material')->findOrFail($id);
        abort_unless($document->status === 'draft', 403);
        $this->resetValidation();
        $this->editingId = $document->id;
        $this->selectedMaterialId = $document->import_material_id;
        $this->articleSearch = (string) $document->material?->article;
        $this->quantity = (string) $document->quantity;
        $this->departmentId = (string) $document->department_id;
        $this->dispatch('material-return-open');
    }

    public function deleteDocument(int $id): void
    {
        DB::transaction(function () use ($id) {
            $document = MaterialReturn::lockForUpdate()->findOrFail($id);
            abort_unless($document->status === 'draft', 403);
            $document->delete();
        });
        $this->resetPage();
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
            $document = $this->editingId
                ? MaterialReturn::lockForUpdate()->findOrFail($this->editingId)
                : new MaterialReturn;
            abort_unless($document->status === 'draft', 403);
            $document->fill([
                'import_material_id' => $data['selectedMaterialId'],
                'quantity' => $data['quantity'],
                'status' => 'draft',
                'department_id' => $data['departmentId'],
            ])->save();
        });

        session(['material_return_department_id' => $data['departmentId']]);
        $this->resetPage();
        $this->reset(['articleSearch', 'quantity', 'selectedMaterialId', 'editingId']);
        $this->dispatch('material-return-close');
        session()->flash('success', 'Повернення збережено. Для зарахування на склад проведіть документ.');
    }

    public function postDocument(int $id): void
    {
        $this->changePostingStatus($id, true);
    }

    public function unpostDocument(int $id): void
    {
        $this->changePostingStatus($id, false);
    }

    private function changePostingStatus(int $id, bool $post): void
    {
        DB::transaction(function () use ($id, $post) {
            $document = MaterialReturn::lockForUpdate()->findOrFail($id);
            $status = $post ? 'posted' : 'draft';

            if ($document->status === $status) {
                return;
            }

            ImportMaterialStock::create([
                'import_material_id' => $document->import_material_id,
                'amount' => $post ? $document->quantity : -$document->quantity,
                'type' => 'return',
                'document_number' => $document->id,
                'document_date' => now(),
                'department_id' => $document->department_id,
            ]);

            $document->update(['status' => $status]);
        });
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

        $returns = MaterialReturn::query()
            ->with(['material.unit', 'department'])
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
