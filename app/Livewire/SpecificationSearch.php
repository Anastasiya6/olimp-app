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

    public $exactMatch = false;

    public function deleteSpecification($id)
    {
        $specification = Specification::findOrFail($id);
        $specification->delete();

        // Отправить сообщение об успешном удалении
        session()->flash('message', 'Запис успішно видалено.');
    }

    public function exactMatch()
    {
        $this->exactMatch = true;
    }

    public function updateSearch()
    {
        $this->resetPage();

        //$this->render();
    }

    public function render()
    {
        if ($this->exactMatch) {
            $searchTerm = '%' . trim($this->searchTerm);
            $searchTermChto = '%' . trim($this->searchTermChto);

        }else{
            $searchTerm = '%' . trim($this->searchTerm) . '%';
            $searchTermChto = '%' . trim($this->searchTermChto) . '%';
        }

        if ($searchTerm == '%%' && $searchTermChto == '%%') {
            $specifications = Specification::with('designations', 'designationEntry')
                ->orderBy('updated_at', 'desc')
                ->paginate(25);
        } else {
            $specifications = Specification::whereHas('designations', function ($query) use ($searchTerm) {
                $query->where('designation', 'like', $searchTerm)
                    ->orderByRaw("CAST(designation AS SIGNED)");
            })
                ->whereHas('designationEntry', function ($query) use ($searchTermChto) {
                    $query->where('designation', 'like', $searchTermChto)
                        ->orderByRaw("CAST(designation AS SIGNED)");
                })
                ->paginate(25);
        }
        return view('livewire.specification-search',compact('specifications'));
    }
}
