<?php

namespace App\Livewire;

use App\Models\Material;
use Livewire\Component;
use Livewire\WithPagination;

class MaterialSearch extends Component
{
    use WithPagination;

    public $searchTerm;

    public function render()
    {
        $searchTerm = '%' . $this->searchTerm . '%';

        $items = Material::where('name', 'like', $searchTerm)->orderBy('updated_at','desc')
            ->orWhere('code', 'like', '%' . $searchTerm . '%')
            ->with('unit')
            ->paginate(15);

        /*$items = Material::whereRaw("MATCH(name) AGAINST(? IN BOOLEAN MODE)", [$searchTerm])
            ->with('unit')
            ->orderBy('updated_at', 'desc')
            ->paginate(15);*/
        $route = 'materials';
        return view('livewire.material-search',compact('items','route'));
    }
}
