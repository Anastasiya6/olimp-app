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

    public ?Specification $editingSpecification = null;

    public int $editFormVersion = 0;

    public function mount()
    {
        $last = Specification::orderBy('updated_at','desc')->with('designations')->first();

        $designation = $last?->designations?->designation ?? '';

        $this->searchTerm = $designation;
        if ((old('_specification_create') || old('_specification_edit')) && session()->has('errors')) {
            $this->setErrorBag(session('errors')->getBag('default'));
        }
        if (old('_specification_edit')) {
            $this->editingSpecification = Specification::with('designations', 'designationEntry')->find(old('_specification_edit'));
        }
    }

    public function editSpecification($id)
    {
        $this->editingSpecification = Specification::with('designations', 'designationEntry')->findOrFail($id);
        $this->editFormVersion++;
        $this->resetValidation();
        $this->dispatch('specification-edit-open');
    }

    public function updatedSearchTerm() { $this->resetPage(); }

    public function updatedSearchTermChto() { $this->resetPage(); }

    public function updatedExactMatch() { $this->resetPage(); }

    public function deleteSpecification($id)
    {
        $specification = Specification::findOrFail($id);
        $specification->delete();
        if ($this->editingSpecification?->id === $specification->id) {
            $this->editingSpecification = null;
        }

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
    }

    protected function specifications()
    {
        if ($this->exactMatch) {
            $searchTerm = '%' . trim($this->searchTerm);
            $searchTermChto = '%' . trim($this->searchTermChto);

        }else{
            $searchTerm = '%' . trim($this->searchTerm) . '%';
            $searchTermChto = '%' . trim($this->searchTermChto) . '%';
        }

        if ($searchTerm == '%%' && $searchTermChto == '%%') {
            return Specification::with('designations', 'designationEntry')
                ->orderBy('updated_at', 'desc')
                ->with('designations', 'designationEntry')
                ->paginate(25);
        } else {

            return Specification
                ::join('designations as designations','specifications.designation_id', '=', 'designations.id')
                ->join('designations as designationsEntry','specifications.designation_entry_id', '=', 'designationsEntry.id')
                ->where('designations.designation', 'like', $searchTerm)
                ->where('designationsEntry.designation', 'like', $searchTermChto)
                ->select('specifications.*')
                ->orderBy('designations.designation')
                ->orderBy('designationsEntry.designation')
                ->with('designations', 'designationEntry')
                ->paginate(25);
            /* $specifications = Specification::whereHas('designations', function ($query) use ($searchTerm) {
                 $query->where('designation', 'like', $searchTerm);
             })
                 ->whereHas('designationEntry', function ($query) use ($searchTermChto) {
                     $query->where('designation', 'like', $searchTermChto);
                 })
                 ->with('designations', 'designationEntry')
                 ->paginate(25);*/
        }
    }

    public function render()
    {

        return view('livewire.specification-search',['specifications' => $this->specifications()]);
    }
}
