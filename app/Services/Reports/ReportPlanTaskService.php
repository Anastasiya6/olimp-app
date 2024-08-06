<?php

namespace App\Services\Reports;
use App\Models\Designation;
use App\Models\PlanTask;
use App\Services\HelpService\PDFService;

class ReportPlanTaskService
{

    public $height = 10;

    public $max_height = 10;

    public $width = array(60,100,30,30,30,30);

    public $header1 = [ 'Номер',
                        'Найменування',
                        "К-ть",
                        'Замовлення',
                        'Цех',
                        'Цех'
                    ];
    public $header2 = [ 'деталі',
                        'деталі',
                        "",
                        '',
                        'відправник',
                        'отримувач'
                        ];
    public $page = 2;

    public $pdf = null;

    public function plan_task($order_name_id,$sender_department,$receiver_department)
    {
        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'План');

        $plan_tasks = PlanTask
            ::where('order_name_id', $order_name_id)
            ->where('sender_department_id', $sender_department)
            ->where('receiver_department_id', $receiver_department)
            ->with('designationEntry')
            ->orderBy('order_designationEntry_letters')
            ->orderBy('order_designationEntry')
            ->get();
        foreach ($plan_tasks as $item) {
           //this->newList();
           // dd($plan_task);
            $this->pdf->Cell($this->width[0], $this->height, $item->designations->designation);
            $this->pdf->Cell($this->width[1], $this->height, $item->designations->name);
            $this->pdf->Cell($this->width[2], $this->height, $item->quantity_total);
            $this->pdf->Cell($this->width[3], $this->height, $item->orderName->name);
            $this->pdf->Cell($this->width[4], $this->height, $item->senderDepartment->number);
            $this->pdf->Cell($this->width[5], $this->height, $item->receiverDepartment->number);
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
