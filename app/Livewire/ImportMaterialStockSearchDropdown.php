<?php

namespace App\Livewire;

use App\Models\ImportMaterial;
use App\Services\ImportMaterialStock\ImportMaterialStock;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ImportMaterialStockSearchDropdown extends Component
{
    public $search = '';

    public $searchResults = [];

    public $selectedMaterial = '';

    public $selectedMaterialId = 0;

    public function searchResult()
    {
        if (strlen($this->search) < 2) {

            $this->searchResults = [];

            return;
        }

        $materials = ImportMaterial::withSum('stocks', 'amount')
        ->where('name', 'like', '%'. $this->search .'%')
            ->orderBy('name')
            ->limit(10)
            ->get()
            ->map(function ($material) {
                $balance = $material->stocks_sum_amount ?? 0;

                return [
                    'id'      => $material->id,
                    'name'    => "{$material->name} | Залишок: {$balance}",
                    'balance' => $balance,
                ];
            });

        $this->searchResults = $materials;

    }

    public function selectSearch($material_id,$material)
    {
        $this->selectedMaterial = $material;
        $this->selectedMaterialId = $material_id;
        $this->searchResults = [];
        $this->search = $material;
        $this->dispatch('materialSelected', id: $this->selectedMaterialId);
    }

    public function render()
    {
        return view('livewire.import-material-stock-search-dropdown');
    }
}
