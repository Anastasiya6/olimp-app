<?php

namespace App\Livewire;

use App\Services\Statements\ApplicationStatementService;
use Livewire\Component;

class Disassembly extends Component
{
    public $isProcessing = false;

    public $order_number;

    public function mount($order_number)
    {
        $this->order_number = $order_number;
    }

    public function generateReport(ApplicationStatementService $service)
    {
        $this->isProcessing = true;

        $service->make($this->order_number);

        $this->isProcessing = false;
    }

    public function render()
    {
        return view('livewire.disassembly');
    }
}
