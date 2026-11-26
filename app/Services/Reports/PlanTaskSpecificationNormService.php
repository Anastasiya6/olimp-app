<?php

namespace App\Services\Reports;
use App\Models\Department;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;
use App\Repositories\Interfaces\OrderNameRepositoryInterface;
use App\Repositories\Interfaces\PlanTaskRepositoryInterface;
use App\Services\HelpService\MaterialService;
use App\Services\HelpService\PDFService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PlanTaskSpecificationNormService
{
    public $width = array(30,120,20,50,50,10);

    public $height = 10;

    public $max_height = 10;

    private array $headerExcel = ['Код 1С',
        'Найменування матеріалів',
        'Од.вимірювання',
        'Норма витрат на виріб',
        'Разом * коеф.',
        'Цех'];

    private array $widthExcel = array(15,60,8,15,15,5);

    // Заголовок таблицы
    public $header1 = ['Код 1С',
        'Найменування матеріалів',
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

    public function specificationNorm($order_name_id,$sender_department_id,$receiver_department_id,$type_report_in,$with_purchased,$with_material_purchased, ?int $sectionId = null)
    {
        $this->sender_department_id = $sender_department_id;

        $this->receiver_department_id = $receiver_department_id;

        $this->order_name_id = $order_name_id;

        $this->sender_department_number = $this->departmentRepository->getByDepartmentIdFirst($this->sender_department_id)?->number;

        $this->receiver_department_number = $this->departmentRepository->getByDepartmentIdFirst($this->receiver_department_id)?->number;

        $records = $this->planTaskRepository->getByOrderDepartments($this->order_name_id,$this->sender_department_id,$this->receiver_department_id,$sectionId);

        if($with_purchased == 1 || $with_material_purchased == 1){
            $records = $records->map(function ($task) use($with_purchased,$with_material_purchased){
                $task->with_purchased = $with_purchased;
                $task->with_material_purchased = $with_material_purchased;
                return $task;
            });
        }

        $records = $this->materialService->material($records,1, $this->sender_department_id, 'material_id',$this->order_name_id);

        if($type_report_in === 'Pdf'){

            $this->getPdf($records, $sectionId);

        }elseif($type_report_in === 'Excel'){

            return $this->getExcel($records, $sectionId);

        }

    }

    private function getPdf($materials, ?int $sectionId)
    {
        $order_number = $this->orderNameRepository->getByOrderFirst($this->order_name_id);

        $heading = PlanTaskReportHeading::make($order_number->name, $this->sender_department_number, $this->receiver_department_number, $sectionId);
        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'СПЕЦИФІКОВАНІ НОРМИ ВИТРАТ МАТЕРІАЛІВ НА ВИРІБ',$heading);

        foreach ($materials as $item) {

            $this->setNewList();

            $this->pdf->MultiCell($this->width[0], $this->height, $item['code_1c'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[1], $this->height, $item['type']=='detail'?$item['material'].' '.$item['detail']:$item['material'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[2], $this->height,$item['unit'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[3], $this->height, $item['sort'] == 0 ? $item['print_number'] . $item['multiplier_str'] . ' = ' :  $item['print_number']/*$item['sort'] == 0 ? $item['quantity_norm']. $multiplier_str . ' = ' : */, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[4], $this->height, $item['sort'] == 0 ? round($item['print_value'] * $item['multiplier'],3) : $item['print_value'] /*$item['sort'] == 0 ? round($item['quantity_norm'] * $multiplier,3) : ''*/, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[5], $this->height, $this->sender_department_number, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->Ln();
        }
        // Выводим PDF в браузер
        $this->pdf->Output('specification_norm_'.$order_number->name.'.pdf', 'I');
    }

    private function getExcel($materials, ?int $sectionId)
    {
        // Новый объект Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $order_number = $this->orderNameRepository->getByOrderFirst($this->order_name_id);
        $heading = PlanTaskReportHeading::make($order_number->name, $this->sender_department_number, $this->receiver_department_number, $sectionId);
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', $heading);


        // Заголовки
        $sheet->setCellValue('A2', $this->headerExcel[0]);
        $sheet->setCellValue('B2', $this->headerExcel[1]);
        $sheet->setCellValue('C2', $this->headerExcel[2]);
        $sheet->setCellValue('D2', $this->headerExcel[3]);
        $sheet->setCellValue('E2', $this->headerExcel[4]);
        $sheet->setCellValue('F2', $this->headerExcel[5]);

        // Устанавливаем стили для заголовков
        $sheet->getStyle('A1:F2')->getFont()->setBold(true);


        $sheet->getColumnDimension('A')->setWidth($this->widthExcel[0]);
        $sheet->getColumnDimension('B')->setWidth($this->widthExcel[1]);
        $sheet->getColumnDimension('C')->setWidth($this->widthExcel[2]);
        $sheet->getColumnDimension('D')->setWidth($this->widthExcel[3]);
        $sheet->getColumnDimension('E')->setWidth($this->widthExcel[4]);
        $sheet->getColumnDimension('F')->setWidth($this->widthExcel[5]);

        // Заполнение данными
        $row = 3;
        foreach ($materials as $item) {

            $sheet->setCellValue('A' . $row, $item['code_1c']);
            $sheet->setCellValue('B' . $row, $item['material']);
            $sheet->setCellValue('C' . $row, $item['unit']);
            $sheet->setCellValue('D' . $row, $item['sort'] == 0 ? $item['print_number'] . $item['multiplier_str'] . ' = ' :  $item['print_number']);
            $sheet->setCellValue('E' . $row,  $item['sort'] == 0 ? $item['print_value'] * $item['multiplier'] : '');
            $sheet->setCellValue('F' . $row, $this->sender_department_number);
            $row++;
        }

        // Сохраните файл
        $writer = new Xlsx($spreadsheet);
        $orderFileName = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $order_number->name);
        $fileName = "plan_{$orderFileName}.xlsx";
        $writer->save($fileName);
        return $fileName;
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
