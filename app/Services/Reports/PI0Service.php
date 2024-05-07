<?php

namespace App\Services\Reports;
use App\Models\Designation;
use App\Services\HelpService\PDFService;

class PI0Service
{

    public $height = 10;

    public $max_height = 10;

    public $width = array(120,200);

    public $header1 = [ 'Номер',
                        "Назва",
                    ];
    public $header2 = [ '',
                        "",
                        ];
    public $page = 2;

    public $pdf = null;

    public function pi0()
    {
        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'ПІ0');
        $pi0s = Designation::where('type',1)
            ->orderBy('designation')
            ->get();

        foreach ($pi0s as $pi0) {
           //this->newList();

            $this->pdf->Cell($this->width[0], $this->height, $pi0->designation);
            $this->pdf->Cell($this->width[1], $this->height, $pi0->name);
            $this->pdf->Ln();

        }

        $pdf_path = storage_path('app/public/pi0.pdf');
        $this->pdf->Output($pdf_path, 'I');
        //
    }


    public function newList()
    {
        if ($this->pdf->getY() >= 185) {
            $this->pdf->Cell(0, 5, 'ЛИСТ ' . $this->page, 0, 1, 'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
            $this->pdf = PDFService::getHeaderPdf($this->pdf, $this->header1, $this->header2, $this->width);
            $this->page++;
        }
    }
}
