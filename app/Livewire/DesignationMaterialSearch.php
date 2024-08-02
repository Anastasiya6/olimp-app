<?php

namespace App\Livewire;

use App\Models\DesignationMaterial;
use Livewire\Component;
use Livewire\WithPagination;

class DesignationMaterialSearch extends Component
{
    use withPagination;

    public $searchTerm;

    public $searchTermMaterial;

    public function deleteDesignationMaterial($id)
    {
        $designationMaterial = DesignationMaterial::findOrFail($id);
        $designationMaterial->delete();

        // Отправить сообщение об успешном удалении
        session()->flash('message', 'Запис успішно видалено.');
    }

    public function render()
    {
        $searchTerm = '%' . trim($this->searchTerm) . '%';

        $searchTermMaterial = '%' . trim($this->searchTermMaterial) . '%';

        if($searchTerm!='%%' || $searchTermMaterial!='%%'){

            $items = DesignationMaterial::whereHas('designation', function ($query) use ($searchTerm) {
                $query->where('designation', 'like', "%$searchTerm%");
            })
                ->whereHas('material', function ($query) use ($searchTermMaterial) {
                    $query->where('name', 'like', "%$searchTermMaterial%");
                })
                ->orderBy('updated_at','desc')
                ->paginate(50);

        }else{

            $items = DesignationMaterial
                ::orderBy('updated_at','desc')
                ->paginate(50);
        }

        $route = 'designation-materials';
        return view('livewire.designation-material-search',compact('items','route'));
    }
}
