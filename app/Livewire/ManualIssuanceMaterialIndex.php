<?php

namespace App\Livewire;

use App\Models\MaterialIssuance;
use App\Models\OrderName;
use Livewire\Component;
use Livewire\WithPagination;

class ManualIssuanceMaterialIndex extends Component
{
    use WithPagination;

    public $reportOrderId = '';

    public function render()
    {
        return view('livewire.manual-issuance-material-index', [
            'order_names' => OrderName::where('is_order', 1)->orderBy('name')->get(),
            'items' => MaterialIssuance::with(['items', 'order_name', 'receivedByUser'])
                ->manual()
                ->whereHas('items')
                ->latest()
                ->paginate(10)
        ]);
    }
}
