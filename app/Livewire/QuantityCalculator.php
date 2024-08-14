<?php

namespace App\Livewire;

use Livewire\Component;

class QuantityCalculator extends Component
{
    public $quantity = '';

    public $quantity_total = '';

    public $order_name_quantity = 0;

    protected $listeners = ['valueGenerated' => 'updateQuantityTotal'];

    public function mount($order_name_quantity)
    {
        $this->order_name_quantity = $order_name_quantity;
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
