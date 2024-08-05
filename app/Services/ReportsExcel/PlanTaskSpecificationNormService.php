<?php

namespace App\Services\ReportsExcel;

use App\Repositories\Interfaces\OrderNameRepositoryInterface;
use App\Repositories\Interfaces\PlanTaskRepositoryInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PlanTaskSpecificationNormService
{
    private $header = ['Код 1С',
        'Найменування матеріалів',
        'Од.вимірювання',
        'Норма витрат на виріб',
        'Разом * 1.2',
        'Цех'];

    private $width = array(15,60,8,15,15,5);

    private PlanTaskRepositoryInterface $planTaskRepositoryInterface;

    private OrderNameRepositoryInterface $orderNameRepository;

    public function __construct(PlanTaskRepositoryInterface $planTaskRepositoryInterface,OrderNameRepositoryInterface $orderNameRepository)
    {
        $this->planTaskRepositoryInterface = $planTaskRepositoryInterface;

        $this->orderNameRepository = $orderNameRepository;
    }

    public function exportExcel($order_name_id,$sender_department_id)
    {
        $items = $this->planTaskRepositoryInterface->getByOrderDepartment($order_name_id,$sender_department_id);

        $groupedData = $this->planTaskRepositoryInterface->getDataByDepartment($items)->sortBy('name');

        // Новый объект Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Заголовки
        $sheet->setCellValue('A1', $this->header[0]);
        $sheet->setCellValue('B1', $this->header[1]);
        $sheet->setCellValue('C1', $this->header[2]);
        $sheet->setCellValue('D1', $this->header[3]);
        $sheet->setCellValue('E1', $this->header[4]);
        $sheet->setCellValue('F1', $this->header[5]);

        // Устанавливаем стили для заголовков
       $sheet->getStyle('A1:F1')->getFont()->setBold(true);


        $sheet->getColumnDimension('A')->setWidth($this->width[0]);
        $sheet->getColumnDimension('B')->setWidth($this->width[1]);
        $sheet->getColumnDimension('C')->setWidth($this->width[2]);
        $sheet->getColumnDimension('D')->setWidth($this->width[3]);
        $sheet->getColumnDimension('E')->setWidth($this->width[4]);
        $sheet->getColumnDimension('F')->setWidth($this->width[5]);

        // Заполнение данными
        $row = 2; // Начинаем с 2 строки, так как 1-я строка занята заголовками
        foreach ($groupedData as $plantask) {
            $sheet->setCellValue('A' . $row, $plantask['code_1c']);
            $sheet->setCellValue('B' . $row, $plantask['name']);
            $sheet->setCellValue('C' . $row, $plantask['unit']);
            $sheet->setCellValue('D' . $row, $plantask['norm']);
            $sheet->setCellValue('E' . $row, $plantask['norm_with_koef']);
            $sheet->setCellValue('F' . $row, $plantask['department']);
            $row++;
        }
        $order_number = $this->orderNameRepository->getByOrderFirst($order_name_id);

        // Сохраните файл
        $writer = new Xlsx($spreadsheet);
        $fileName = "plan_$order_number->name.xlsx";
        $writer->save($fileName);
        return $fileName;

    }
}
