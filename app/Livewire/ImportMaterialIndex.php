<?php

namespace App\Livewire;

use App\Models\ImportMaterial;
use App\Models\TypeUnit;
use Illuminate\Validation\Rule;
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

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->reset('name', 'code', 'article', 'type_unit_id');
        $this->resetValidation();
        $this->dispatch('open-modal', 'create-import-material');
    }

    public function save(): void
    {
        $this->name = trim($this->name);
        $this->code = trim($this->code);
        $this->article = trim($this->article);

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255', Rule::unique('import_materials', 'code')
                ->where(fn ($query) => $query->where('article', $this->article === '' ? null : $this->article))],
            'article' => ['nullable', 'string', 'max:255'],
            'type_unit_id' => ['required', 'integer', 'exists:type_units,id'],
        ]);

        $data['code'] = $this->code === '' ? null : $this->code;
        $data['article'] = $this->article === '' ? null : $this->article;
        ImportMaterial::create($data);

        $this->reset('name', 'code', 'article', 'type_unit_id', 'search');
        $this->resetPage();
        $this->dispatch('close-modal', 'create-import-material');
        session()->flash('success', 'Матеріал 1С додано.');
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
