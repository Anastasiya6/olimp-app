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
        $searchTerm = '%' . $this->searchTerm . '%';
        $searchTermChto = '%' . $this->searchTermChto . '%';

        if($searchTerm=='%%' || $searchTermChto='%%'){

            $items = Designation::where('type',0)
                ->orderBy('updated_at','desc')
                ->paginate(25);

        }else {
            $items = Designation::where(function ($query) use ($searchTerm, $searchTermChto) {
                $query->
                where('name', 'like', $searchTerm)
                    ->where('designation', 'like', $searchTermChto)
                    ->where('type', 0)
                    ->orderByRaw("CAST(designation AS SIGNED)");
            })
                ->paginate(50);
        }
        $route = 'designations';
        return view('livewire.designation-search',compact('items','route'));
    }
}
