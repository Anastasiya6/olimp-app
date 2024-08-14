<?php

namespace App\Livewire;

use App\Models\DesignationMaterial;
use App\Models\GroupMaterial;
use App\Models\Material;
use Livewire\Component;
use Livewire\WithPagination;

class MaterialSearch extends Component
{
    use WithPagination;

    public $searchTerm;

    public $material_message;

    public function viewConfirm($type)
    {
        if($type == 0){
            $this->material_message = "Матеріал прив'язаний до деталі. Видалити неможливо";

        }elseif($type == 1){
            $this->material_message = 'Запис успішно видалено.';
        }
        $this->dispatch('open-modal',name:'viewLog');
    }

    public function deleteMaterial($id)
    {
        $material = Material::findOrFail($id);

        $searchInDesignationMaterial = DesignationMaterial::where('material_id',$material->id)->first();

        $searchInGroupMaterial = GroupMaterial::where('material_id',$material->id)->first();

        if(isset($searchInDesignationMaterial->id) || isset($searchInGroupMaterial->id)){
            $this->viewConfirm(0);
           // session()->flash('message', "Матеріал прив'язаний до деталі. Видалити неможливо");
        }else{
            $material->delete();
            $this->viewConfirm(1);
            // Отправить сообщение об успешном удалении
          //  session()->flash('message', 'Запис успішно видалено.');
        }

    }
    protected function materials()
    {
        $searchTerm = '%' . trim($this->searchTerm) . '%';
        return Material::where('name', 'like', $searchTerm)->orderBy('updated_at','desc')
            ->orWhere('code', 'like', '%' . $searchTerm . '%')
            ->with('unit')
            ->paginate(15);
    }
    public function render()
    {
        return view('livewire.material-search',[
            'items' => $this->materials(),
            'route' => 'materials'
        ]);
    }
}
