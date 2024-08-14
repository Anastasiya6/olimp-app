<?php

namespace App\Services\Reports;
use App\Models\Department;
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
        'Разом * 1.2',
        'Цех'];

    private array $widthExcel = array(15,60,8,15,15,5);

    // Заголовок таблицы
    public $header1 = ['Код 1С',
        'Найменування матеріалів',
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

    public function specificationNorm($order_name_id,$sender_department_id,$type_report_in)
    {
        $this->sender_department_id = $sender_department_id;

        $this->order_name_id = $order_name_id;

        $this->department_number = Department::find($this->sender_department_id)->number;

        $records = $this->planTaskRepository->getByOrderDepartment($this->order_name_id,$this->sender_department_id);

        $records = $this->materialService->material($records,1,'material_id');

        if($type_report_in === 'Pdf'){

            $this->getPdf($records);

        }elseif($type_report_in === 'Excel'){

            return $this->getExcel($records);

        }

    }

    private function getPdf($materials)
    {
        $order_number = $this->orderNameRepository->getByOrderFirst($this->order_name_id);

        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'СПЕЦИФІКОВАНІ НОРМИ ВИТРАТ МАТЕРІАЛІВ НА ВИРІБ',' ЗАМОВЛЕННЯ №'.$order_number->name);

        foreach ($materials as $item) {

            //dd($item);

            $this->setNewList();

            $this->pdf->MultiCell($this->width[0], $this->height, $item['code_1c'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[1], $this->height, $item['material'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[2], $this->height,$item['unit'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[3], $this->height, $item['sort'] == 0 ? $item['quantity_norm'].' * 1.2 = ' : $item['norm'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[4], $this->height, $item['sort'] == 0 ? $item['quantity_norm'] * 1.2 : '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[5], $this->height, $this->department_number, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->Ln();
        }
        // Выводим PDF в браузер
        $this->pdf->Output('specification_norm_'.$order_number->name.'.pdf', 'I');
    }

    private function getExcel($materials)
    {
        // Новый объект Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Заголовки
        $sheet->setCellValue('A1', $this->headerExcel[0]);
        $sheet->setCellValue('B1', $this->headerExcel[1]);
        $sheet->setCellValue('C1', $this->headerExcel[2]);
        $sheet->setCellValue('D1', $this->headerExcel[3]);
        $sheet->setCellValue('E1', $this->headerExcel[4]);
        $sheet->setCellValue('F1', $this->headerExcel[5]);

        // Устанавливаем стили для заголовков
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);


        $sheet->getColumnDimension('A')->setWidth($this->widthExcel[0]);
        $sheet->getColumnDimension('B')->setWidth($this->widthExcel[1]);
        $sheet->getColumnDimension('C')->setWidth($this->widthExcel[2]);
        $sheet->getColumnDimension('D')->setWidth($this->widthExcel[3]);
        $sheet->getColumnDimension('E')->setWidth($this->widthExcel[4]);
        $sheet->getColumnDimension('F')->setWidth($this->widthExcel[5]);

        // Заполнение данными
        $row = 2; // Начинаем с 2 строки, так как 1-я строка занята заголовками
        foreach ($materials as $item) {
            $sheet->setCellValue('A' . $row, $item['code_1c']);
            $sheet->setCellValue('B' . $row, $item['material']);
            $sheet->setCellValue('C' . $row, $item['unit']);
            $sheet->setCellValue('D' . $row, $item['sort'] == 0 ? $item['norm'].' * 1.2 = ' : $item['norm']);
            $sheet->setCellValue('E' . $row, $item['sort'] == 0 ? $item['norm'] * 1.2 : '');
            $sheet->setCellValue('F' . $row, $this->department_number);
            $row++;
        }
        $order_number = $this->orderNameRepository->getByOrderFirst($this->order_name_id);

        // Сохраните файл
        $writer = new Xlsx($spreadsheet);
        $fileName = "plan_$order_number->name.xlsx";
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
