<?php

namespace App\Services\Reports;
use App\Models\DeliveryNote;
use App\Services\HelpService\PDFService;

class DeliveryNoteDesignationService
{
    public $width = array(25,50,30,30,25,15,30,30,30);

    public $height = 10;

    public $max_height = 10;
    // Заголовок таблицы
    public $header1 = ['Номер ',
                        'Номер деталі',
                        'Дата',
                        'Дата',
                        'Замовлення',
                        'Кіл-ть',
                        'Цех',
                        'Цех',
                        'З'
                        ];
    public $header2 = ['документу',
                        '',
                        'документу',
                        'внесення',
                        '',
                        '',
                        'відправник',
                        'отримувач',
                        'покупними'];
    public $pdf = null;

    public $page = 2;

    public $designation;

    public function deliveryNoteDesignation($designation)
    {
        $this->designation = $designation;

        $items = $this->getDeliveryNotesItems();

        $this->getPdf($items);

    }
    public function getPdf($items)
    {

        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'ЗДАТОЧНІ','ДЕТАЛЬ '.$this->designation);

        // Добавление данных таблицы
        foreach ($items as $item) {

            if($this->pdf->getY() >= 185) {
                $this->pdf->Cell(0, 5, 'ЛИСТ '.$this->page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                $this->pdf = PDFService::getHeaderPdf($this->pdf, $this->header1, $this->header2, $this->width);
                $this->page++;
            }
            $this->pdf->MultiCell($this->width[0], $this->height, $item->document_number, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[1], $this->height, $item->designation->designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[2], $this->height, \Carbon\Carbon::parse($item->document_date)->format('d.m.Y')??'', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[3], $this->height, \Carbon\Carbon::parse($item->created_at)->format('d.m.Y')??'', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[4], $this->height, $item->orderName->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[5], $this->height, $item->quantity, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[6], $this->height, $item->senderDepartment->number, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[7], $this->height, $item->receiverDepartment->number, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[8], $this->height, $item->with_purchased==0? 'Ні' : 'Так', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->Ln();
        }

        // Выводим PDF в браузер
        $this->pdf->Output('delivery_note_'.$this->designation.'.pdf', 'I');
    }

    private function getDeliveryNotesItems()
    {
        return DeliveryNote::whereHas('designation', function ($query) {
            $query->where('designation', 'like', $this->designation)
                ->orderByRaw("CAST(designation AS SIGNED)");
        })
            ->with('orderName')
            ->orderBy('document_number')
            ->get();
    }
}
