<?php

namespace App\Services\Reports;
use App\Models\Department;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;
use App\Repositories\Interfaces\OrderNameRepositoryInterface;
use App\Repositories\Interfaces\PlanTaskRepositoryInterface;
use App\Services\HelpService\MaterialService;
use App\Services\HelpService\PDFService;

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
        'Разом * коеф.',
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

    public $receiver_department_id;

    public $sender_department_number;

    public $receiver_department_number;

    private PlanTaskRepositoryInterface $planTaskRepository;

    private OrderNameRepositoryInterface $orderNameRepository;

    private DepartmentRepositoryInterface $departmentRepository;

    public MaterialService $materialService;

    public function __construct(PlanTaskRepositoryInterface $planTaskRepository, OrderNameRepositoryInterface $orderNameRepository, MaterialService $service,DepartmentRepositoryInterface $departmentRepository)
    {
        $this->planTaskRepository = $planTaskRepository;

        $this->orderNameRepository = $orderNameRepository;

        $this->materialService = $service;

        $this->departmentRepository = $departmentRepository;

    }

    public function detailSpecificationNorm($order_name_id,$sender_department_id,$receiver_department_id,$type_report_in,$with_purchased,$with_material_purchased)
    {
        $this->sender_department_id = $sender_department_id;

        $this->receiver_department_id = $receiver_department_id;

        $this->order_name_id = $order_name_id;

        $this->sender_department_number = $this->departmentRepository->getByDepartmentIdFirst($this->sender_department_id)?->number;

        $this->receiver_department_number = $this->departmentRepository->getByDepartmentIdFirst($this->receiver_department_id)?->number;

        $records = $this->planTaskRepository->getByOrderDepartments($this->order_name_id,$this->sender_department_id,$this->receiver_department_id);

        if($with_purchased == 1 || $with_material_purchased == 1){
            $records = $records->map(function ($task) use($with_purchased,$with_material_purchased){
                $task->with_purchased = $with_purchased;
                $task->with_material_purchased = $with_material_purchased;
                return $task;
            });
        }

        $records = $this->materialService->material($records,1,$this->sender_department_id,'detail');

        if($type_report_in === 'Pdf'){

            $this->getPdf($records);

        }
    }

    private function getPdf($materials)
    {
        $order_number = $this->orderNameRepository->getByOrderFirst($this->order_name_id);

        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'ПОДЕТАЛЬНО-СПЕЦИФІКОВАНІ НОРМИ ВИТРАТ МАТЕРІАЛІВ НА ВИРІБ',' З цеха '.$this->sender_department_number.' у цех '.$this->receiver_department_number.' ЗАМОВЛЕННЯ №'.$order_number->name);

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

                $this->pdf->MultiCell($this->width[3], $this->height, $item['sort'] == 0 ? $item['print_number'] . $item['multiplier_str'] . ' = ' :  $item['print_number']/*$item['sort'] == 0 ? $item['quantity_norm'] . $multiplier_str . ' = ' : $item['quantity_norm']*/, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                $this->pdf->MultiCell($this->width[4], $this->height,  $item['sort'] == 0 ? round($item['print_value'] * $item['multiplier'],3) : $item['print_value'] /*$item['sort'] == 0 ? round($item['quantity_norm'],3) * $multiplier : round($item['quantity_norm'],3) */ , 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                $this->pdf->MultiCell($this->width[5], $this->height, $this->sender_department_number, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

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
