<?php

namespace App\Livewire;

use App\Models\Designation;
use Livewire\Component;
use Livewire\WithPagination;

class Pi0Search extends Component
{
    use WithPagination;

    public $searchTerm;

    public $searchTermChto;

    public function updateSearch()
    {
        $this->resetPage();

        $this->render();
    }

    public function render()
    {
        $this->searchTerm = str_replace('і', 'i', $this->searchTerm);

        $this->earchTermChto = str_replace('і', 'i', $this->searchTermChto);

        $searchTerm = '%' . $this->searchTerm . '%';

        $searchTermChto = '%' . $this->searchTermChto . '%';

        if($searchTerm!='%%' || $searchTermChto!='%%'){

            $items = Designation::with('unit')->where(function ($query) use ($searchTerm, $searchTermChto) {
                $query->
                where('name', 'like', $searchTerm)
                    ->where('designation', 'like', $searchTermChto)
                    ->where('type', 1);
            })
                ->orderBy("name")
                ->paginate(50);


        }else {
            $items = Designation::where('type',1)
                ->orderBy('updated_at','desc')
                ->with('unit')
                ->paginate(25);
        }

        $route = 'pi0s';
        return view('livewire.pi0-search',compact('items','route'));
    }
}
