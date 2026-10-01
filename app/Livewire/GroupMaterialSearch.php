<?php

namespace App\Livewire;

use App\Models\GroupMaterial;
use Livewire\Component;
use Livewire\WithPagination;

class GroupMaterialSearch extends Component
{
    use WithPagination;

    public $searchTerm;

    public ?GroupMaterial $editingGroupMaterial = null;

    public int $editFormVersion = 0;

    public function mount()
    {
        if ((old('_group_material_create') || old('_group_material_edit')) && session()->has('errors')) {
            $this->setErrorBag(session('errors')->getBag('default'));
        }
        if (old('_group_material_edit')) {
            $this->editingGroupMaterial = GroupMaterial::with('material', 'materialEntry')->find(old('_group_material_edit'));
        }
    }

    public function editGroupMaterial($id)
    {
        $this->editingGroupMaterial = GroupMaterial::with('material', 'materialEntry')->findOrFail($id);
        $this->editFormVersion++;
        $this->resetValidation();
        $this->dispatch('group-material-edit-open');
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function deleteGroupMaterial($id)
    {
        $groupMaterial = GroupMaterial::findOrFail($id);
        $groupMaterial->delete();
        if ($this->editingGroupMaterial?->id === $groupMaterial->id) {
            $this->editingGroupMaterial = null;
        }

        // Отправить сообщение об успешном удалении
        session()->flash('message', 'Запис успішно видалено.');
    }

    protected function groupMaterials()
    {
        $searchTerm = '%' . trim($this->searchTerm) . '%';

        return GroupMaterial::whereHas('material', function ($query) use ($searchTerm) {
            $query->where('name', 'like', $searchTerm)
                ->orderBy("name");
        })->with('material','materialEntry')
            ->paginate(25);
    }

    public function render()
    {
        return view('livewire.group-material-search',[
            'items' => $this->groupMaterials(),
            'route' => 'group-materials'
        ]);
    }
}
