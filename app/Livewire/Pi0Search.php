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

    public $sortField;

    public $sortAsc = false;

    protected $queryString = ['searchTerm','searchTermChto','sortAsc','sortField'];

    public function sortBy($field)
    {
        if($this->sortField === $field){
            $this->sortAsc = !$this->sortAsc;
        }else{
            $this->sortAsc = true;
        }
        $this->sortField = $field;
    }

    public function updateSearch()
    {
        $this->resetPage();

        $this->render();
    }

    public function render()
    {
        $this->searchTerm = $this->replace_i_with_і($this->searchTerm);

        $this->searchTermChto = $this->replace_i_with_і($this->earchTermChto);

        $searchTerm = '%' . $this->searchTerm . '%';

        $searchTermChto = '%' . $this->searchTermChto . '%';


        if($searchTerm!='%%' || $searchTermChto!='%%'){

            if($this->sortField){
                $orderBy = $this->sortField;
            }else{
                $orderBy = 'name';
            }

            $items = Designation::with('unit')->where(function ($query) use ($searchTerm, $searchTermChto) {
                $query->
                where('name', 'like', $searchTerm)
                    ->where('designation', 'like', $searchTermChto)
                    ->where('type', 1);
            })
                ->orderBy($orderBy,$this->sortAsc ? 'asc' : 'desc')
                ->paginate(50);


        }else {
            if($this->sortField){
                $orderBy = $this->sortField;
            }else{
                $orderBy = 'updated_at';
            }
            $items = Designation::where('type',1)
                ->orderBy($orderBy,$this->sortAsc ? 'asc' : 'desc')
                ->with('unit')
                ->paginate(25);
        }

        $route = 'pi0s';
        return view('livewire.pi0-search',compact('items','route'));
    }

    function replace_i_with_і($text) {
        // Проверяем, есть ли в тексте буква "i" в русском тексте
        if (preg_match('/[А-Яа-я]*[iI][А-Яа-я]*/u', $text)) {
            // Если есть, заменяем "i" на "і"
            $text = str_replace('i', 'і', $text);
            $text = str_replace('I', 'І', $text);
        }
        return $text;
    }
}
