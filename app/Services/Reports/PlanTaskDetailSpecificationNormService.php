<?php

namespace App\Services\Reports;
use App\Models\Department;
use App\Repositories\Interfaces\OrderNameRepositoryInterface;
use App\Repositories\Interfaces\PlanTaskRepositoryInterface;
use App\Services\HelpService\MaterialService;
use App\Services\HelpService\PDFService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PlanTaskDetailSpecificationNormService
{
    public $width = array(100,70,20,50,50,10);

    public $height = 10;

    public $max_height = 10;

    // Заголовок таблицы
    public $header1 = ['Матеріал',
        'Номер деталі',
        'Од.',
        'Норма витрат на виріб',
        'Разом * 1.2',
        'Цех'];
    public $header2 = ['',
        '',
        'вимір.',
        '',
        '',
        ''];
    public $pdf = null;

    public $page = 1;

    public $order_name_id;

    public $first_department = 1;

    public $sender_department_id;

    public $department_number;

    private PlanTaskRepositoryInterface $planTaskRepository;

    private OrderNameRepositoryInterface $orderNameRepository;

    public MaterialService $materialService;

    public function __construct(PlanTaskRepositoryInterface $planTaskRepository,OrderNameRepositoryInterface $orderNameRepository, MaterialService $service)
    {
        $this->planTaskRepository = $planTaskRepository;

        $this->orderNameRepository = $orderNameRepository;

        $this->materialService = $service;

    }

    public function detailSpecificationNorm($order_name_id,$sender_department_id,$type_report_in)
    {
        $this->sender_department_id = $sender_department_id;

        $this->order_name_id = $order_name_id;

        $this->department_number = Department::find($this->sender_department_id)->number;

        $records = $this->planTaskRepository->getByOrderDepartment($this->order_name_id,$this->sender_department_id);

        $records = $this->materialService->material($records,1,'detail');

        if($type_report_in === 'Pdf'){

            $this->getPdf($records);

        }
    }

    private function getPdf($materials)
    {
        $order_number = $this->orderNameRepository->getByOrderFirst($this->order_name_id);

        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'ПОДЕТАЛЬНО-СПЕЦИФІКОВАНІ НОРМИ ВИТРАТ МАТЕРІАЛІВ НА ВИРІБ',' ЗАМОВЛЕННЯ №'.$order_number->name);

        foreach ($materials as $material=>$group) {


            $this->pdf->MultiCell($this->width[0], $this->height, $material, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[1], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[2], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[3], $this->height,'', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[4], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[5], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->Ln();

            $this->setNewList();
            foreach($group as $item) {

                $this->pdf->MultiCell($this->width[0], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                $this->pdf->MultiCell($this->width[1], $this->height, $item['detail'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                $this->pdf->MultiCell($this->width[2], $this->height, $item['unit'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                $this->pdf->MultiCell($this->width[3], $this->height, $item['quantity_norm'] . ' * 1.2 = ', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                $this->pdf->MultiCell($this->width[4], $this->height, $item['quantity_norm'] * 1.2 , 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                $this->pdf->MultiCell($this->width[5], $this->height, $this->department_number, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                $this->pdf->Ln();
                $this->setNewList();
            }
        }
        // Выводим PDF в браузер
        $this->pdf->Output('specification_norm_'.$order_number->name.'.pdf', 'I');
    }

    private function setNewList()
    {
        if($this->pdf->getY() >= 180) {
            $this->pdf->Cell(0, 10, 'ЛИСТ '.$this->page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
            $this->pdf = PDFService::getHeaderPdf($this->pdf, $this->header1, $this->header2, $this->width);
            $this->page++;
        }
    }
}