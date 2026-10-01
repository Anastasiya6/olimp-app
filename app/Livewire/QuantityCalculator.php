<?php

namespace App\Livewire;

use Livewire\Component;

class QuantityCalculator extends Component
{
    public $quantity = '';

    public $quantity_total = '';

    public $order_name_quantity = 0;

    protected $listeners = ['valueGenerated' => 'updateQuantityTotal'];

    public function mount($order_name_quantity, $restore_input = false)
    {
        $this->order_name_quantity = $order_name_quantity;
        if ($restore_input) {
            $this->quantity = old('quantity', '');
            $this->quantity_total = old('quantity_total', '');
        }
    }

    public function updateQuantityTotal($value)
    {
        $this->quantity_total = intval($value) * $this->order_name_quantity;
    }

    public function searchResult()
    {
        $this->dispatch('valueGenerated',$this->quantity);
    }

    public function render()
    {
        return view('livewire.quantity-calculator');
    }
}
