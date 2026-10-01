<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\OrderName;
use App\Services\HelpService\NoMaterialService;
use Illuminate\Database\Query\JoinClause;
use Livewire\Component;
use Livewire\WithPagination;

class OrderIndex extends Component
{
    use WithPagination;

    public string $orderSearch = '';
    public string $detailSearch = '';
    public ?Order $editingOrder = null;
    public int $editFormVersion = 0;

    public function mount()
    {
        if ((old('_order_create') || old('_order_edit')) && session()->has('errors')) {
            $this->setErrorBag(session('errors')->getBag('default'));
        }
        if (old('_order_edit')) {
            $this->editingOrder = Order::with('designation')->find(old('_order_edit'));
        }
    }

    public function editOrder($id)
    {
        $this->editingOrder = Order::with('designation')->findOrFail($id);
        $this->editFormVersion++;
        $this->resetValidation();
        $this->dispatch('order-edit-open');
    }

    public function deleteOrder($id): void
    {
        Order::findOrFail($id)->delete();
        session()->flash('status', 'Дані успішно видалено');
    }

    public function updatedOrderSearch()
    {
        $this->resetPage();
    }

    public function updatedDetailSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $items = Order
            ::when(trim($this->orderSearch) !== '', fn ($query) => $query->whereHas('orderName', fn ($order) => $order->where('name', 'like', '%'.trim($this->orderSearch).'%')))
            ->when(trim($this->detailSearch) !== '', fn ($query) => $query->whereHas('designation', fn ($detail) => $detail->where('designation', 'like', '%'.trim($this->detailSearch).'%')))
            ->orderBy('updated_at','desc')
            ->orderBy('order_name_id','desc')
            ->with('orderName','designation','designationMaterial.material')
            ->paginate(10);

        $items->transform(function ($item) {

            if($item->designationMaterial->isEmpty()){
                $item->is_material = 0;
            }

            $item->is_material = NoMaterialService::noMaterial($item->designation_id, $item->designationMaterial->isNotEmpty());

            return $item;
        });

         return view('livewire.order-index', [
            'items' => $items,
            'route' => 'orders',
            'order_names' => OrderName::all(),
            'report_dates' => Order::leftJoin('report_application_statements', function (JoinClause $join) {
                 $join->on('orders.order_name_id', '=', 'report_application_statements.order_name_id')
                     ->on('orders.designation_id', 'report_application_statements.designation_id')
                     ->on('orders.designation_id', 'report_application_statements.designation_entry_id');
            })
                 ->pluck('report_application_statements.created_at', 'orders.id')
                 ->toArray(),
            'title' => 'Розузловання']);
    }
}
