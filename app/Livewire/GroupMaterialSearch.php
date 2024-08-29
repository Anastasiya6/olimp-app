<?php

namespace App\Livewire;

use App\Models\GroupMaterial;
use Livewire\Component;
use Livewire\WithPagination;

class GroupMaterialSearch extends Component
{
    use WithPagination;

    public $searchTerm;

    public function deleteGroupMaterial($id)
    {
        $groupMaterial = GroupMaterial::findOrFail($id);
        $groupMaterial->delete();

        // Отправить сообщение об успешном удалении
        session()->flash('message', 'Запис успішно видалено.');
    }

    protected function groupMaterials()
    {
        $searchTerm = '%' . trim($this->searchTerm) . '%';

        return $items = GroupMaterial::whereHas('material', function ($query) use ($searchTerm) {
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
