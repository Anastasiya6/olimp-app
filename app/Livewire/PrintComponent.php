<?php

namespace App\Livewire;

use Mpdf\Mpdf;
use Livewire\Component;

class PrintComponent extends Component
{
    public $data;

    public function render()
    {
        return view('livewire.print-component');
    }

    public function generatePDF()
    {


    }
}
