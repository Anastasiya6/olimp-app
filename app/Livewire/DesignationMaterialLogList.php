<?php

namespace App\Livewire;

use App\Models\DesignationMaterialLog;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class DesignationMaterialLogList extends Component
{
    use WithPagination;

    public $searchTerm;

    public $selectedLog;

    public $designation_number;

    public function updateSearch()
    {
        $this->resetPage();
    }

    public function viewLog($id,$designation_number){

        $this->designation_id = $id;

        $this->designation_number = $designation_number;

        $this->selectedLog = DesignationMaterialLog
            ::where('designation_id',$id)
            ->orderBy('created_at', 'desc')
            ->get();

        $this->dispatch('open-modal',name:'viewLog');
    }

    public function render()
    {
        $searchTerm = '%' . trim($this->searchTerm) . '%';

        if($searchTerm!='%%'){

            $items = DesignationMaterialLog::select(
                'designation_id',
                DB::raw('MIN(designation_number) as designation_number'),
                DB::raw('MIN(designation) as designation'),
                DB::raw('MAX(created_at) as created_at'))
                ->groupBy('designation_id')
                ->where('designation_number', 'like', $searchTerm)
                ->orderBy('created_at', 'desc')
                ->paginate(25);

        }else {
            $items = DesignationMaterialLog::select(
                'designation_id',
                DB::raw('MIN(designation_number) as designation_number'),
                DB::raw('MIN(designation) as designation'),
                DB::raw('MAX(created_at) as created_at'))
                ->groupBy('designation_id')
                ->orderBy('created_at', 'desc')
                ->paginate(25);
        }

        return view('livewire.designation-material-log-list',['items' => $items]);
    }
}
