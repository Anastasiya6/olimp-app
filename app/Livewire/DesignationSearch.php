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

    public function updateSearch()
    {
        $this->resetPage();
    }

    protected function designations()
    {
        $searchTerm = '%' . trim($this->searchTerm) . '%';

        $searchTermChto = '%' . trim($this->searchTermChto) . '%';

        if($searchTerm!='%%' || $searchTermChto!='%%'){

            return Designation::where(function ($query) use ($searchTerm, $searchTermChto) {
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
            return Designation::where('type',0)
                ->orderBy('updated_at','desc')
                ->paginate(25);
        }
    }

    public function render()
    {
        return view('livewire.designation-search',[
            'items' => $this->designations(),
            'route'=> 'designations'
        ]);
    }
}
