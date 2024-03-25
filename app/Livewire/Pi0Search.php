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

    public function render()
    {
        $searchTerm = '%' . $this->searchTerm . '%';
        $searchTermChto = '%' . $this->searchTermChto . '%';

        if($searchTerm!='%%' || $searchTermChto!='%%'){

            $items = Designation::where(function ($query) use ($searchTerm, $searchTermChto) {
                $query->
                where('name', 'like', $searchTerm)
                    ->where('designation', 'like', $searchTermChto)
                    ->where('type', 1)
                    ->orderByRaw("CAST(designation AS SIGNED)");
            })
                ->paginate(50);


        }else {
            $items = Designation::where('type',1)
                ->orderBy('updated_at','desc')
                ->paginate(25);
        }

        $route = 'pi0s';
        return view('livewire.pi0-search',compact('items','route'));
    }
}
