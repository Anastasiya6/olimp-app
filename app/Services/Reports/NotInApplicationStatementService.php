<?php

namespace App\Services\Reports;

use App\Models\DeliveryNote;
use App\Services\HelpService\PDFService;

class NotInApplicationStatementService
{

    public $height = 10;

    public $max_height = 10;

    public $width = array(70,150,30);

    public $header1 = [ 'Номер',
                        "Назва",
                        "Замовлення"
                    ];
    public $header2 = [ '',
                        "",
                        ""
                        ];
    public $page = 2;

    public $pdf = null;

    public function notInApplicationStatement()
    {
        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'Відсутні у відомості застосування');
        $records = DeliveryNote::select('designation_id', 'designations.designation','designations.name','order_names.name as order_name')
            ->distinct() // Унікальні записи
            ->join('order_names', 'order_names.id', '=', 'delivery_notes.order_name_id') // JOIN з order_names
            ->join('designations', 'designations.id', '=', 'delivery_notes.designation_id')
            ->whereNotIn('designation_id', function ($query) {
                $query->select('designation_entry_id')
                    ->from('report_application_statements'); // Підзапит
            })
            ->orderBy('order_names.name') // Сортування за іменем
            ->orderBy('designations.designation')
            ->orderBy('designation_id') // Сортування за designation_id
            ->get();
       // dd($records);
        foreach ($records as $record) {

            $this->pdf->Cell($this->width[0], $this->height, $record->designation);
            $this->pdf->Cell($this->width[1], $this->height, $record->name);
            $this->pdf->Cell($this->width[2], $this->height, $record->order_name);

            $this->pdf->Ln();

        }

        $pdf_path = storage_path('app/public/pi0.pdf');
        $this->pdf->Output($pdf_path, 'I');

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
