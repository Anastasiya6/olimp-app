<?php

namespace App\Livewire;

use App\Models\ImportMaterialStaging;
use Livewire\Component;

class ViewMaterialConflict extends Component
{
    public function render()
    {
        return view('livewire.view-material-conflict', [
            'items' => ImportMaterialStaging::with('unit')
                ->where('status', 'conflict')
                ->orderByDesc('id')
                ->paginate(10),
        ]);
    }
}
