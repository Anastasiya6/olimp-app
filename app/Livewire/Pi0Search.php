<?php

namespace App\Livewire;

use App\Models\Designation;
use App\Models\TypeUnit;
use Livewire\Component;
use Livewire\WithPagination;

class Pi0Search extends Component
{
    use WithPagination;

    public $searchTerm;

    public $searchTermChto;

    public $sortField;

    public $sortAsc = false;

    public ?Designation $editingPi0 = null;

    public int $editFormVersion = 0;

    public function mount()
    {
        if ((old('_pi0_create') || old('_pi0_edit')) && session()->has('errors')) {
            $this->setErrorBag(session('errors')->getBag('default'));
        }
        if (old('_pi0_edit')) {
            $this->editingPi0 = Designation::where('type', 1)->find(old('_pi0_edit'));
        }
    }

    public function editPi0($id)
    {
        $this->editingPi0 = Designation::where('type', 1)->findOrFail($id);
        $this->editFormVersion++;
        $this->resetValidation();
        $this->dispatch('pi0-edit-open');
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedSearchTermChto()
    {
        $this->resetPage();
    }

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

        //$this->render();
    }

    public function render()
    {
        $searchTerm = '%' . trim($this->searchTerm) . '%';

        $searchTermChto = '%' . trim($this->searchTermChto) . '%';

        if($searchTerm!='%%' || $searchTermChto!='%%'){

            $sortAsc = $this->sortAsc ? 'asc' : 'desc';

            if($this->sortField){
                $orderBy = $this->sortField;
            }else{
                $orderBy = 'name';
                $sortAsc = 'asc';
            }

            $items = Designation::with('unit')
                ->where(function ($query) use ($searchTerm, $searchTermChto) {
                    $query->where(function ($query) use ($searchTerm) {
                        $query->where('name', 'like', $searchTerm)
                            ->orWhere('gost', 'like', $searchTerm);
                    })->where('designation', 'like', $searchTermChto)
                        ->where('type', 1);
                })
                ->orderBy($orderBy,$sortAsc)
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
        $units = TypeUnit::all();
        return view('livewire.pi0-search',compact('items','route', 'units'));
    }
}
