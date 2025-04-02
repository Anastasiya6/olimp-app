<?php

namespace App\Services\Reports;
use App\Models\Task;
use App\Services\HelpService\NoMaterialService;
use App\Services\HelpService\PDFService;

class TaskNoMaterialService
{
    public $width = array(25,25,45,90,10);

    public $height = 10;

    public $max_height = 10;
    // Заголовок таблицы
    public $header1 = ['Номер',
                        'Дата',
                        'Номер',
                        'Назва деталі',
                        'К-ть'];
    public $header2 = ['документа',
                        'документа',
                        'деталі',
                        '',
                        ''
];
    public $pdf = null;

    public $page = 2;

    public $sender_department_id;

    public $selectedItems = [];

    public function taskNoMaterial($sender_department)
    {
        $this->sender_department_id = $sender_department;

        $this->getRecords();

        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'ВІДСУТНІ МАТЕРІАЛИ',"",'P');

        $this->getDetailPdf($this->selectedItems);

    }

    private function getDetailPdf($records)
    {
        foreach ($records as $item) {

            $this->setNewList();

            $this->pdf->MultiCell($this->width[0], $this->height, $item->document_number, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[1], $this->height, \Carbon\Carbon::parse($item->document_date)->format('d.m.Y'), 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[2], $this->height, $item->designation->designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[3], $this->height, $item->designation->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[4], $this->height, $item->quantity, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->Ln();
        }
        // Выводим PDF в браузер
        $this->pdf->Output('task_no_material.pdf', 'I');
    }

    private function setNewList()
    {
        if($this->pdf->getY() >= 270) {
            $this->pdf->Cell(0, 10, 'ЛИСТ '.$this->page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
            $this->pdf = PDFService::getHeaderPdf($this->pdf, $this->header1, $this->header2, $this->width);
            $this->page++;
        }
    }

    private function getRecords()
    {
        $items = Task
            ::where('department_id',$this->sender_department_id)
            ->with('designationMaterial.material')
            ->get();

       foreach($items as $item){

            $item->material = 1;

            if($item->designationMaterial->isEmpty()){
                $item->material = 0;
            }
            $item->material = NoMaterialService::noMaterial($item->designation_id,$item->designationMaterial->isNotEmpty());
            if($item->material == 0){
                $this->selectedItems[] = $item;
            }
        }
    }

}
