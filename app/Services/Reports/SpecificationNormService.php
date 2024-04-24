<?php

namespace App\Services\Reports;
use App\Models\ReportApplicationStatement;
use App\Services\HelpService\PDFService;
use TCPDF;

class SpecificationNormService
{
    public function specificationNorm($order_number)
    {
        $items = ReportApplicationStatement
            ::where('order_number',$order_number)
            ->has('designationMaterial.material')
            ->with('designationEntry','designationMaterial')
            ->get();

        $data = $items->map(function ($item) {
            return [
                'id' => $item->designationMaterial->material->id,
                'name' => $item->designationMaterial->material->name,
                'unit' => $item->designationMaterial->material->unit->unit,
                'norm' => $item->designationMaterial->norm * $item->quantity_total,
                'department' => substr($item->designationEntry->route, 0, 2),
            ];
        });

        $groupedData = $data->groupBy('id')->map(function ($items) {
            return [
                'name' => $items->first()['name'], // Берем название материала из первого элемента группы
                'unit' => $items->first()['unit'], // Берем единицу измерения из первого элемента группы
                'department' => $items->first()['department'], // Берем цех из первого элемента группы
                'norm' => $items->sum('norm'), // Суммируем количество по всем элементам группы
            ];
        });
        $groupedData = $groupedData->sortBy('name');

        $this->getPdf($groupedData,$order_number);

    }
    public function getPdf($groupedData,$order_number)
    {
        $width = array(120,30,70,10);

        // Заголовок таблицы
        $header1 = ['Найменування матеріалів',
            'Од.виміру',
            'Норма витрат на виріб',
            'Цех'];
        $header2 = ['',
            '',
            '',
            ''];
        $pdf = PDFService::getPdf($header1,$header2,$width,'Специфіковані норми витрат матеріалів на виріб',' ЗАКАЗ №'.$order_number);
        $page = 2;
        // Добавление данных таблицы
        foreach ($groupedData as $item) {
            if($pdf->getY() >= 185) {
                $pdf->Cell(0, 5, 'ЛИСТ '.$page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                $pdf = PDFService::getHeaderPdf($pdf, $header1, $header2, $width);
                $page++;
            }
            $pdf->Cell($width[0], 10, $item['name']);
            $pdf->Cell($width[1], 10, $item['unit']);
            $pdf->Cell($width[2], 10, $item['norm']);
            $pdf->Cell($width[3], 10, $item['department']);
            $pdf->Ln();
        }

        // Выводим PDF в браузер
        $pdf->Output('specification_norm.pdf', 'I');
    }
}
