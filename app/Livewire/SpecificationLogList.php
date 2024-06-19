<?php

namespace App\Livewire;

use App\Models\SpecificationLog;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class SpecificationLogList extends Component
{
    use WithPagination;

    public $searchTerm;

    public $selectedLog;

    public $designation_number;

    public function viewLog($id,$designation_number){

        $this->designation_id = $id;

        $this->designation_number = $designation_number;

        $this->selectedLog = SpecificationLog
            ::where('designation_id',$id)
            ->orderBy('created_at', 'desc')
            ->get();

        $this->dispatch('open-modal',name:'viewLog');
    }

    public function updateSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $searchTerm = '%' . trim($this->searchTerm) . '%';

        if($searchTerm!='%%'){

            $items = SpecificationLog::select(
                'designation_id',
                DB::raw('MIN(designation_number) as designation_number'),
                DB::raw('MIN(designation) as designation'),
                DB::raw('MAX(created_at) as created_at'))
                ->groupBy('designation_id')
                ->where('designation_number', 'like', $searchTerm)
                ->orderBy('created_at', 'desc')
                ->paginate(5);

        }else{

            $items = SpecificationLog::select(
                'designation_id',
                DB::raw('MIN(designation_number) as designation_number'),
                DB::raw('MIN(designation) as designation'),
                DB::raw('MAX(created_at) as created_at'))
                ->groupBy('designation_id')
                ->orderBy('created_at', 'desc')
                ->paginate(25);
        }

        return view('livewire.specification-log-list',['items' => $items]);
    }
}
