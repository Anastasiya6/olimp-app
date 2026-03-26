<?php

namespace App\Livewire;

use App\Models\MaterialIssuance;
use Livewire\Component;
use Livewire\WithPagination;

class IssuanceMaterialIndex extends Component
{
    use WithPagination;
    public function render()
    {
        return view('livewire.issuance-material-index', [
            'items' => MaterialIssuance::latest()->paginate(10)
        ]);
    }
}
