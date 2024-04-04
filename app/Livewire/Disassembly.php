<?php

namespace App\Livewire;

use App\Services\Statements\ApplicationStatementService;
use Livewire\Component;

class Disassembly extends Component
{
    public $isProcessing = false;

    public function generateReport(ApplicationStatementService $service)
    {
        $this->isProcessing = true;

        $service->make();

        $this->isProcessing = false;
    }

    public function render()
    {
        return view('livewire.disassembly');
    }
}
