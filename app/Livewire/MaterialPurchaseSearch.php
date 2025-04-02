<?php

namespace App\Livewire;

use App\Models\MaterialPurchase;
use App\Models\Purchase;
use Livewire\Component;
use Livewire\WithPagination;

class MaterialPurchaseSearch extends Component
{
    use WithPagination;

    public $route = 'material-purchases';

    public $searchTerm;

    public $searchTermChto;

    public function updateSearch()
    {
        $this->resetPage();
    }

    public function deletePurchase($id)
    {
        $purchase = MaterialPurchase::findOrFail($id);
        $purchase->delete();

        // Отправить сообщение об успешном удалении
        session()->flash('message', 'Запис успішно видалено.');
    }

    protected function purchases()
    {
        $searchTerm = '%' . trim($this->searchTerm) . '%';

        $searchTermChto = '%' . trim($this->searchTermChto) . '%';

        if ($searchTerm == '%%' && $searchTermChto == '%%') {
            $purchases = MaterialPurchase::with('designation', 'designationEntry')
                ->orderBy('updated_at', 'desc')
                ->paginate(25);
        } else {
            $purchases = MaterialPurchase::whereHas('designation', function ($query) use ($searchTerm) {
                $query->where('designation', 'like', $searchTerm)
                    ->orderByRaw("CAST(designation AS SIGNED)");
            })
                ->whereHas('designationEntry', function ($query) use ($searchTermChto) {
                    $query->where('designation', 'like', $searchTermChto)
                        ->orderByRaw("CAST(designation AS SIGNED)");
                })
                ->paginate(25);
        }

        return $purchases;
    }
    public function render()
    {
        return view('livewire.material-purchase-search',[
            'items' => $this->purchases(),
            'route' => $this->route,
        ]);
    }
}
