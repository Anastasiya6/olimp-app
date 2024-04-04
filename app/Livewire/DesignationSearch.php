<?php

namespace App\Livewire;

use App\Models\Designation;
use Livewire\Component;
use Livewire\WithPagination;

class DesignationSearch extends Component
{
    use WithPagination;

    public $searchTerm;

    public $searchTermChto;

    public function render()
    {
        $this->searchTerm = str_replace('і', 'i', $this->searchTerm);

        $this->earchTermChto = str_replace('і', 'i', $this->searchTermChto);

        $searchTerm = '%' . $this->searchTerm . '%';

        $searchTermChto = '%' . $this->searchTermChto . '%';

        if($searchTerm!='%%' || $searchTermChto!='%%'){

            $items = Designation::where(function ($query) use ($searchTerm, $searchTermChto) {
                $query->
                where(function ($query) use ($searchTerm) {
                    $query->where('name', 'like', $searchTerm)
                        ->orWhereNull('name');
                })
                    ->where('designation', 'like', $searchTermChto)
                    ->where('type', 0)
                    ->orderByRaw("CAST(designation AS SIGNED)");
            })
                ->paginate(50);


        }else {
            $items = Designation::where('type',0)
                ->orderBy('updated_at','desc')
                ->paginate(25);
        }
        $route = 'designations';
        return view('livewire.designation-search',compact('items','route'));
    }
}
