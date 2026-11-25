<?php

namespace App\Livewire;

use App\Models\ImportMaterial;
use App\Models\TypeUnit;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class ImportMaterialIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $name = '';
    public string $code = '';
    public string $article = '';
    public $type_unit_id = '';
    #[Locked]
    public ?int $editingMaterialId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->reset('name', 'code', 'article', 'type_unit_id', 'editingMaterialId');
        $this->resetValidation();
        $this->dispatch('open-modal', 'create-import-material');
    }

    public function openEdit(int $id): void
    {
        $material = ImportMaterial::findOrFail($id);
        $this->editingMaterialId = $material->id;
        $this->name = $material->name;
        $this->code = $material->code ?? '';
        $this->article = $material->article ?? '';
        $this->type_unit_id = $material->type_unit_id ?? '';

        $this->resetValidation();
        $this->dispatch('open-modal', 'create-import-material');
    }

    public function save(): void
    {
        $this->name = trim($this->name);
        $this->code = trim($this->code);
        $this->article = trim($this->article);
        $material = $this->editingMaterialId === null
            ? new ImportMaterial()
            : ImportMaterial::findOrFail($this->editingMaterialId);
        $uniqueCode = Rule::unique('import_materials', 'code')
            ->where(fn ($query) => $query->where('article', $this->article === '' ? null : $this->article));
        if ($material->exists) {
            $uniqueCode->ignore($material);
        }

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255', $uniqueCode],
            'article' => ['nullable', 'string', 'max:255'],
            'type_unit_id' => [$material->exists ? 'nullable' : 'required', 'integer', 'exists:type_units,id'],
        ]);

        $data['code'] = $this->code === '' ? null : $this->code;
        $data['article'] = $this->article === '' ? null : $this->article;
        $data['type_unit_id'] = $data['type_unit_id'] === '' ? null : $data['type_unit_id'];
        $editing = $material->exists;
        $material->fill($data)->save();

        $this->reset('name', 'code', 'article', 'type_unit_id', 'editingMaterialId');
        if (!$editing) {
            $this->reset('search');
            $this->resetPage();
        }
        $this->dispatch('close-modal', 'create-import-material');
        session()->flash('success', $editing ? 'Матеріал 1С оновлено.' : 'Матеріал 1С додано.');
    }

    public function render()
    {
        $search = trim($this->search);

        return view('livewire.import-material-index', [
            'items' => ImportMaterial::with('unit')
                ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('code', 'like', '%'.$search.'%')
                        ->orWhere('article', 'like', '%'.$search.'%');
                }))
                ->orderByDesc('id')->paginate(25),
            'units' => TypeUnit::orderBy('unit')->get(),
        ]);
    }
}
