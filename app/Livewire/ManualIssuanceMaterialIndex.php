<?php

namespace App\Livewire;

use App\Models\MaterialIssuance;
use App\Models\MaterialIssuanceItem;
use App\Models\ImportMaterialStock;
use App\Models\OrderName;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ManualIssuanceMaterialIndex extends Component
{
    use WithPagination;

    public $reportOrderId = '';

    public bool $postedOnly = false;

    public ?string $reportDateFrom = null;

    public ?string $reportDateTo = null;

    public function postDocument(int $id): void
    {
        DB::transaction(function () use ($id) {
            $document = MaterialIssuance::manual()->lockForUpdate()->findOrFail($id);

            if ($document->status === 'posted') {
                return;
            }

            $items = MaterialIssuanceItem::where('material_issuance_id', $document->id)->get();

            foreach ($items as $item) {
                ImportMaterialStock::create([
                    'import_material_id' => $item->import_material_id,
                    'amount' => -$item->quantity,
                    'type' => 'stock_out',
                    'document_number' => $document->id,
                ]);
            }

            $document->update(['status' => 'posted']);
        });
    }

    public function unpostDocument(int $id): void
    {
        DB::transaction(function () use ($id) {
            $document = MaterialIssuance::manual()->lockForUpdate()->findOrFail($id);

            if ($document->status !== 'posted') {
                return;
            }

            $items = MaterialIssuanceItem::where('material_issuance_id', $document->id)->get();

            foreach ($items as $item) {
                ImportMaterialStock::create([
                    'import_material_id' => $item->import_material_id,
                    'amount' => $item->quantity,
                    'type' => 'stock_in',
                    'document_number' => $document->id,
                ]);
            }

            $document->update(['status' => 'draft']);
        });
    }

    public function deleteDocument(int $id): void
    {
        DB::transaction(function () use ($id) {
            $document = MaterialIssuance::manual()->lockForUpdate()->findOrFail($id);

            if ($document->status === 'posted') {
                $items = MaterialIssuanceItem::where('material_issuance_id', $document->id)->get();

                foreach ($items as $item) {
                    ImportMaterialStock::create([
                        'import_material_id' => $item->import_material_id,
                        'amount' => $item->quantity,
                        'type' => 'stock_in',
                        'document_number' => $document->id,
                    ]);
                }
            }

            $document->update(['status' => 'draft']);
            $document->delete();
        });
    }

    public function render()
    {
        return view('livewire.manual-issuance-material-index', [
            'order_names' => OrderName::where('is_order', 1)->orderBy('name')->get(),
            'items' => MaterialIssuance::with(['items.importMaterial', 'order_name', 'receivedByUser'])
                ->manual()
                ->whereHas('items')
                ->latest()
                ->paginate(10)
        ]);
    }
}
