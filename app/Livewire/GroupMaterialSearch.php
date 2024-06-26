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

    public function render()
    {
        $searchTerm = '%' . $this->searchTerm . '%';

        $items = GroupMaterial::whereHas('material', function ($query) use ($searchTerm) {
                $query->where('name', 'like', $searchTerm)
                    ->orderBy("name");
                })->paginate(25);

        $route = 'group-materials';
        return view('livewire.group-material-search',compact('items','route'));
    }
}
