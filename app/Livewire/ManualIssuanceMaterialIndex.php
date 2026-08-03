<?php

namespace App\Livewire;

use App\Models\MaterialIssuance;
use Livewire\Component;
use Livewire\WithPagination;

class ManualIssuanceMaterialIndex extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.manual-issuance-material-index', [
            'items' => MaterialIssuance::with('items')
                ->manual()
                ->whereHas('items')
                ->latest()
                ->paginate(10)
        ]);
    }
}
