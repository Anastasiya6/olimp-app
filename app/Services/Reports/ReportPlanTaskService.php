<?php

namespace App\Services\Reports;
use App\Models\Designation;
use App\Models\PlanTask;
use App\Services\HelpService\NoMaterialService;
use App\Services\HelpService\PDFService;

class ReportPlanTaskService
{

    public $height = 10;

    public $max_height = 10;

    public $width = array(10,60,100,30,30,25,25);

    public $header1 = [ 'Є',
                        'Номер',
                        'Найменування',
                        "К-ть",
                        'Замовлення',
                        'Цех',
                        'Цех'
                    ];
    public $header2 = [ 'матер.',
                        'деталі',
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
            ->when($receiver_department!= 0, function ($query) use ($receiver_department) {
                return $query->where('receiver_department_id', $receiver_department);
            })
            ->with('designationEntry')
            ->orderBy('order_designationEntry')
            ->orderBy('order_designationEntry_letters')
            ->get();

        foreach ($plan_tasks as $item) {

            if($item->designationMaterial->isEmpty()){
                $item->material = 0;
            }
            $item->material = NoMaterialService::noMaterial($item->designation_id,$item->designationMaterial->isNotEmpty());

            $this->pdf->Cell($this->width[0], $this->height, $item->material == 0 ? '-': '+');
            $this->pdf->Cell($this->width[1], $this->height, $item->designation->designation);
            $this->pdf->Cell($this->width[2], $this->height, $item->designation->name);
            $this->pdf->Cell($this->width[3], $this->height, $item->quantity);
            $this->pdf->Cell($this->width[4], $this->height, $item->orderName->name);
            $this->pdf->Cell($this->width[5], $this->height, $item->senderDepartment->number);
            $this->pdf->Cell($this->width[6], $this->height, $item->receiverDepartment->number);
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
