<?php

namespace App\Livewire;

use App\Services\Statements\ApplicationStatementService;
use Livewire\Component;

class Disassembly extends Component
{
    public $isProcessing = false;

    public $order_name_id;

    public function mount($order_name_id)
    {
        $this->order_name_id = $order_name_id;
    }

    public function generateReport(ApplicationStatementService $service)
    {
        $this->isProcessing = true;

        $service->make($this->order_name_id);

        $this->isProcessing = false;

        $this->dispatch('reportGenerated',$this->order_name_id,now()->toDateTimeString());
    }

    public function render()
    {
        return view('livewire.disassembly');
    }
}
