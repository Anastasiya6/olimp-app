<?php

namespace App\Livewire;

use App\Models\Specification;
use Livewire\Component;
use Livewire\WithPagination;

class SpecificationSearch extends Component
{
    use WithPagination;

    public $searchTerm;

    public $searchTermChto;

    public function deleteSpecification($id)
    {
        $specification = Specification::findOrFail($id);
        $specification->delete();

        // Отправить сообщение об успешном удалении
        session()->flash('message', 'Запись успешно удалена.');
    }

    public function render()
    {
        $searchTerm = '%' . $this->searchTerm . '%';
        $searchTermChto = '%' . $this->searchTermChto . '%';
        if($searchTerm=='%%' && $searchTermChto='%%'){

            $specifications = Specification::with('designations','designationEntry')
                ->orderBy('updated_at','desc')
                ->paginate(50);

        }else {

            $specifications = Specification::whereHas('designations', function ($query) use ($searchTerm) {
                $query->where('designation', 'like', $searchTerm);
            })
                ->whereHas('designationEntry', function ($query) use ($searchTermChto) {
                    $query->where('designation', 'like', $searchTermChto);
                })
                ->orderByRaw("CAST(designation AS SIGNED)")
                ->paginate(50);
        }
        return view('livewire.specification-search',compact('specifications'));
    }
}
