<?php

namespace App\Livewire;

use App\Models\ImportMaterialStock;
use App\Models\MaterialIssuance;
use App\Models\MaterialIssuanceItem;
use Livewire\Component;
use Livewire\WithPagination;

class IssuanceMaterialIndex extends Component
{
    use WithPagination;
    public function postDocument($id)
    {
        $doc = MaterialIssuance::findOrFail($id);

        if ($doc->status === 'posted') {
            return;
        }

        // отримати items
        $items = MaterialIssuanceItem::where('material_issuance_id', $doc->id)->get();

        foreach ($items as $item) {

            // списання зі складу (твій сервіс)
            ImportMaterialStock::create([
                'import_material_id' => $item->import_material_id,
                'amount' => -$item->quantity,
                'type' => 'stock_out',
                'document_number' => $item->material_issuance_id
            ]);
        }

        $doc->update([
            'status' => 'posted'
        ]);
    }
    public function render()
    {
        return view('livewire.issuance-material-index', [
            'items' => MaterialIssuance::with('items')
                ->whereHas('items')
                ->latest()
                ->paginate(10)
        ]);
    }
}
