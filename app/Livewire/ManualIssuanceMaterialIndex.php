<?php

namespace App\Livewire;

use App\Models\MaterialIssuance;
use App\Models\MaterialIssuanceItem;
use App\Models\ImportMaterialStock;
use App\Models\Department;
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

    public ?int $editingDocumentId = null;

    public bool $documentFormLoaded = false;

    public int $documentFormVersion = 0;

    public function openDocument(?int $id = null): void
    {
        if ($id !== null) {
            $document = MaterialIssuance::manual()->findOrFail($id);
            abort_if($document->status === 'posted', 403);
        }

        $this->editingDocumentId = $id;
        $this->documentFormLoaded = true;
        $this->documentFormVersion++;
        $this->dispatch('manual-issuance-document-open');
    }

    public string $filterDocumentNumber = '';

    public string $filterOrder = '';

    public function updatedFilterDocumentNumber(): void
    {
        $this->resetPage();
    }

    public function updatedFilterOrder(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset('filterDocumentNumber', 'filterOrder');
        $this->resetPage();
    }

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
                    'department_id' => $document->department_id,
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
                    'department_id' => $document->department_id,
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
                        'department_id' => $document->department_id,
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
            'departments' => Department::orderBy('number')->get(),
            'items' => MaterialIssuance::with(['items.importMaterial', 'order_name', 'department', 'receivedByUser'])
                ->manual()
                ->whereHas('items')
                ->when($this->filterOrder !== '', function ($query) {
                    $query->where('order_name_id', $this->filterOrder);
                })
                ->when(trim($this->filterDocumentNumber) !== '', function ($query) {
                    $query->where('id', (int) trim($this->filterDocumentNumber));
                })
                ->latest()
                ->paginate(10)
        ]);
    }
}
