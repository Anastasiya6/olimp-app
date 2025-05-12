<?php

namespace App\Services\Reports;
use App\Models\Task;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;
use App\Services\HelpService\MaterialService;
use App\Services\HelpService\PDFService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TaskService
{
    public $width = array(35,50,10,30,80,10,40,30);

    public $width1 = array(16,50,100,30);

    public $height = 10;

    public $max_height = 10;
    // Заголовок таблицы
    public $header1 = ['Номер',
                        'Назва',
                        'К-ть',
                        'Код 1C',
                        'Матеріал',
                        'Од',
                        'Норма',
                        'Норма*коеф.'];
    public $header2 = [ 'деталі',
                        'деталі',
                        '',
                        '',
                        '',
                        '.вим.',
                        '',
                        ''];
    public $header3 = [
        'Номер',
        'Номер деталі',
        'Назва деталі',
        'Кількість'
  ];
    public $header4 = [
        'докумен.',
        '',
        '',
        ''
 ];
    private array $headerExcel = [
        'Код 1С',
        'Найменування деталі',
        'Найменування матеріалів',
        'Од.вимірювання',
        'Норма витрат на виріб',
        'Разом * коеф.',
        'Цех'];

    private array $widthExcel = array(15,30,60,8,15,15,5);

    public $pdf = null;

    public $page = 2;

    public $sender_department_id;

    public $records;

    public $materialService;

    public $type_report = 0;

    public $sender_department_number;

    public $without_coefficient;

    public $ids;

    private DepartmentRepositoryInterface $departmentRepository;

    public function __construct( MaterialService $service, DepartmentRepositoryInterface $departmentRepository )
    {
        $this->materialService = $service;

        $this->departmentRepository = $departmentRepository;
    }

    public function task($parameters)
    {
       // dd($parameters);
        $type_report_in = $parameters?->type_report_in??'pdf';

        $this->without_coefficient = $parameters?->without_coefficient??0;

        $this->ids = $parameters?->ids;

        $this->type_report = $parameters?->type_report??0;

        $this->sender_department_id = $parameters?->sender_department??0;

        $this->sender_department_number = $this->departmentRepository->getByDepartmentIdFirst($this->sender_department_id)?->number;

        if($this->without_coefficient == 1) {

            if($type_report_in == 'pdf'){
                $this->header1[7] = $this->header1[6];
            }else{
                $this->headerExcel[5] = 'Норма';
            }
        }

        $records = $this->getRecords();

        /*Report by detail-specification*/
        if($this->type_report == 0) {

            $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'Подетальні-специфіковані ',' ');

            $this->getDetailSpecificationPdf($records);

        /*Report together by materials*/
        }elseif($this->type_report == 1){

            $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'Разом по матеріалам','');

            if($type_report_in == 'pdf'){

                $this->getMaterialPdf($records);

            }else{

                return $this->getMaterialExcel($records);

            }

            /*Report by details*/
        }elseif($this->type_report == 2){

            $this->pdf = PDFService::getPdf($this->header3,$this->header4,$this->width1,'Завдання ',' ','P');

            $this->getDetailPdf($records);

        }
    }

    private function getDetailPdf($records)
    {
        foreach ($records as $item) {

            $this->setNewList($this->header3,$this->header4,$this->width1,270);

            $this->pdf->MultiCell($this->width1[0], $this->height, $item->document_number, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width1[1], $this->height, $item->designation->designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width1[2], $this->height, $item->designation->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width1[3], $this->height, $item->quantity, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->Ln();
        }

        // Выводим PDF в браузер
        $this->pdf->Output('task_no_detail_pdf', 'I');
    }

    private function getMaterialExcel($materials)
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
        $sheet->setCellValue('G1', $this->headerExcel[6]);

        // Устанавливаем стили для заголовков
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);


        $sheet->getColumnDimension('A')->setWidth($this->widthExcel[0]);
        $sheet->getColumnDimension('B')->setWidth($this->widthExcel[1]);
        $sheet->getColumnDimension('C')->setWidth($this->widthExcel[2]);
        $sheet->getColumnDimension('D')->setWidth($this->widthExcel[3]);
        $sheet->getColumnDimension('E')->setWidth($this->widthExcel[4]);
        $sheet->getColumnDimension('F')->setWidth($this->widthExcel[5]);
        $sheet->getColumnDimension('G')->setWidth($this->widthExcel[6]);

        // Заполнение данными
        $row = 2; // Начинаем с 2 строки, так как 1-я строка занята заголовками
        foreach ($materials as $item) {

            $sheet->setCellValue('A' . $row, $item['code_1c']);
            $sheet->setCellValue('B' . $row, $item['detail']);
            $sheet->setCellValue('C' . $row, $item['material']);
            $sheet->setCellValue('D' . $row, $item['unit']);
            $sheet->setCellValue('E' . $row, $item['sort'] == 0 ? $item['print_number']. $item['multiplier_str'] .' = ' : $item['print_number']);
            $sheet->setCellValue('F' . $row, $item['sort'] == 0 ? round($item['print_value'] * $item['multiplier'],3) : '');
            $sheet->setCellValue('G' . $row, $this->sender_department_number);
            $row++;
        }
// Вирівнювання всіх комірок по лівому краю
        $sheet->getStyle('A1:G' . ($row - 1))
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        // Сохраните файл
        $writer = new Xlsx($spreadsheet);
        $fileName = "task.xlsx";
        $writer->save($fileName);
        return $fileName;
    }

    private function getDetailSpecificationPdf($records)
    {
        foreach ($records as $item_record) {

            $this->setNewList($this->header1,$this->header2,$this->width);

            $this->pdf->MultiCell($this->width[0], $this->height, $item_record->designation->designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[1], $this->height, $item_record->designation->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[2], $this->height, $item_record->quantity, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[3], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $first = 0;

            if($item_record->materials) {

                $materials = $item_record->materials->sortBy('material')->sortBy('sort');

                foreach ($materials as $item) {
                    $column = 3;
                    $first++;

                    $this->setNewList($this->header1,$this->header2,$this->width);

                    if ($first > 1) {

                        $this->pdf->Ln();

                        for($i=0;$i<=$column;$i++) {
                            $this->pdf->MultiCell($this->width[$i], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                        }

                    }

                    $this->pdf->MultiCell($this->width[++$column], $this->height, $item['material'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                    $this->pdf->MultiCell($this->width[++$column], $this->height, $item['unit'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                    if($this->without_coefficient == 1) {

                        $item['multiplier_str'] = '';

                        $item['multiplier'] = 1;
                    }

                    $this->pdf->MultiCell($this->width[++$column], $this->height, $item['sort'] == 0 ? $item['print_number'] . $item['multiplier_str'] . ' = ' :  $item['print_number']/*$norm->sort == 0 ? $norm->norm . ' * ' . $norm->quantity . ' * ' . $item->quantity .' * '. $norm->pred_quantity_node. $multiplier_str . ' = ' : $norm->norm . ' * ' . $item->quantity . ' = '*/, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                    $this->pdf->MultiCell($this->width[++$column], $this->height, $item['sort'] == 0 ? round($item['print_value'] * $item['multiplier'],3) : $item['print_value']/*$norm->sort == 0 ? round($norm->norm * $norm->quantity * $item->quantity * $norm->pred_quantity_node * $multiplier,3) : round($norm->norm * $item->quantity,3)*/, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                }
            }

            $this->pdf->Ln();
        }

        // Выводим PDF в браузер
        $this->pdf->Output('task_detail_specification'.'.pdf', 'I');
    }

    private function setNewList($header1, $header2,$width,$height=180)
    {
        if($this->pdf->getY() >= $height) {
            $this->pdf->Ln();
            $this->pdf->Cell(0, 10, 'ЛИСТ '.$this->page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
            $this->pdf = PDFService::getHeaderPdf($this->pdf, $header1, $header2, $width);
            $this->page++;
        }
    }

    private function getRecords()
    {
        if($this->ids){
            $records = Task
                ::whereIn('id',$this->ids)
                ->with('designationMaterial.material','designationMaterial.designation')
                ->get();

            return $this->getMaterials($records);
        }

        return array();
    }

    private function getMaterials($records)
    {
        return $this->materialService->material($records,$this->type_report,$this->sender_department_id,'material_id');

    }

    private function getMaterialPdf($materials)
    {
        $this->pdf->SetFont('dejavusans', 'B', 14);

        $this->pdf->Cell(0, 10, "Разом по матеріалам",0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку

        $this->pdf->SetFont('dejavusans', '', 10);

        foreach ($materials as $item) {

            $this->setNewList($this->header1,$this->header2,$this->width);

            $this->pdf->MultiCell($this->width[0], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[1], $this->height, $item['detail'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[2], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[3], $this->height, $item['code_1c'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[4], $this->height, $item['material'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[5], $this->height,$item['unit'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            if($this->without_coefficient == 1) {

                $item['multiplier_str'] = '';

                $item['multiplier'] = 1;
            }

            $this->pdf->MultiCell($this->width[6], $this->height, $item['sort'] == 0 ? $item['print_number'] . $item['multiplier_str'] . ' = ' :  $item['print_number'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[7], $this->height, $item['sort'] == 0 ? $item['print_value'] * $item['multiplier'] : $item['print_value'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->Ln();
        }

        $this->pdf->Output('task_materials_.pdf', 'I');
    }
}
