<?php

namespace App\Livewire;

use App\Models\ImportMaterialStock;
use App\Models\MaterialIssuance;
use App\Models\MaterialIssuanceItem;
use App\Models\OrderName;
use DB;
use Livewire\Attributes\Session;
use Livewire\Component;
use Livewire\WithPagination;

class IssuanceMaterialIndex extends Component
{
    use WithPagination;

    public $selectedItems = [];

    #[Session]
    public $designation_number;

    public $selectedOrder = null;

    public function mount()
    {
        if($this->designation_number == '')
            $this->designation_number = 'ААМВ685614100';

    }

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

    public function unpostDocument($id)
    {
        $doc = MaterialIssuance::findOrFail($id);

        if ($doc->status !== 'posted') {
            return;
        }

        $items = MaterialIssuanceItem::where('material_issuance_id', $doc->id)->get();

        DB::transaction(function () use ($doc, $items) {

            foreach ($items as $item) {

                ImportMaterialStock::create([
                    'import_material_id' => $item->import_material_id,
                    'amount' => $item->quantity, // 🔥 ПЛЮС замість мінуса
                    'type' => 'stock_in',
                    'document_number' => $doc->id
                ]);
            }

            $doc->update([
                'status' => 'draft'
            ]);
        });
    }

    public function render()
    {
        $order_names = OrderName::where('is_order', 1)->orderBy('name')->get();

        if (!$this->selectedOrder && $order_names->count()) {
            $this->selectedOrder = $order_names->first()->id;
        }

        return view('livewire.issuance-material-index', [
            'order_names'=> $order_names,
            'items' => MaterialIssuance::with('items')
                ->byDesignation()
                ->whereHas('items')
                ->latest()
                ->paginate(10)
        ]);
    }
}
