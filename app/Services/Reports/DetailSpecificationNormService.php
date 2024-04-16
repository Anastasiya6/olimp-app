<?php

namespace App\Services\Reports;
use App\Models\ReportApplicationStatement;
use App\Services\HelpService\PDFService;
use App\Services\Statements\ApplicationStatementPrintService;
use TCPDF;

class DetailSpecificationNormService
{
    public function detailSpecificationNorm($department,$order_number)
    {
        $data = $this->getData($department,$order_number);

        $this->getPdf($data,$department,$order_number);

    }

    public function getData($department,$order_number)
    {
        $items = ReportApplicationStatement
            ::where('order_number',$order_number)
            ->whereHas('designation', function ($query) use ($department){
            $query-> whereRaw("SUBSTRING(route, 1, 2) = '$department'");
        })
            ->has('designationMaterial.material')
            ->with('designationEntry','designationMaterial.material')
            ->get();

        $data = $items->sortBy('designationMaterial.material.id')->map(function ($item) {
            return [
                'id' => $item->designationMaterial->material->id,
                'material_name' => $item->designationMaterial->material->name,
                'detail_name' => $item->designationEntry->designation,
                'quantity_total' => $item->quantity_total,
                'unit' => $item->designationMaterial->material->unit->unit,
                'norm' => $item->designationMaterial->norm,
            ];
        });

        return $data;
    }

    public function getPdf($data,$department,$order_number)
    {
        $width = array(80,40,30,20,50,50);
        $header1 = ['Найменування матеріалу',
            'Найменування DSE',
            'Застосовність',
            'Од.виміру',
            'Норма витрат',
            'Норма на застосування'];

        $header2 = [ '',
            '',
            '',
            '',
            'на един',
            ''];

        $pdf = PDFService::getPdf($header1,$header2,$width,'ПОДЕТАЛЬНО-СПЕЦИФІКОВАННІ НОРМИ ВИТРАТ МАТЕРІАЛІВ НА ВИРІБ','ЦЕХ '.$department.' ЗАКАЗ '.$order_number);
        $page = 2;

        // Устанавливаем стиль линии
        $pdf->SetLineStyle(array('dash' => 2, 'color' => array(0, 0, 0))); // Пунктирная линия черного цвета

        // Устанавливаем шрифт и размер текста
        $pdf->SetFont('dejavusans', '', 10);
        $sum_norm = 0;

        $first = 1;
        // Переменная для хранения предыдущего материала
        $previousMaterial = null;
        foreach ($data as $row) {

            if($pdf->getY() >= 185) {
                $pdf->Cell(0, 5, 'ЛИСТ '.$page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                $pdf = PDFService::getHeaderPdf($pdf, $header1, $header2, $width);
                $page++;
            }
            // Если текущий материал отличается от предыдущего, добавляем его в PDF
            if ($row['id'] !== $previousMaterial) {

                if($first > 1){
                    $pdf->Cell(270, 10, 'Разом по матер. '.$sum_norm,0,1,'R');
                    $pdf->Ln();
                }
                $first++;
                $sum_norm = 0;

                if($pdf->getY() >= 185) {
                    PDFService::getList($page,$pdf, $header1, $header2, $width);
                    $page++;
                }
                // Добавляем название материала
                $pdf->Cell(100, 10, $row['material_name']);
                $pdf->Ln();
                if($pdf->getY() >= 185) {
                    PDFService::getList($page,$pdf, $header1, $header2, $width);
                    $page++;
                }

                // Сбрасываем предыдущий материал
                $previousMaterial = $row['id'];
            }
            // Если текущий материал такой же, как предыдущий, то добавляем только детали без названия материала
            $pdf->Cell($width[0], 10, '');
            $pdf->Cell($width[1], 10, $row['detail_name']);
            $pdf->Cell($width[2], 10, $row['quantity_total']);
            $pdf->Cell($width[3], 10, $row['unit']);
            $pdf->Cell($width[4], 10, $row['norm']);
            $pdf->Cell($width[5], 10, $row['norm']*$row['quantity_total']);
            $pdf->Ln();

            $sum_norm = $sum_norm + $row['norm']*$row['quantity_total'];

        }
        // Выводим PDF в браузер
        $pdf->Output('example.pdf', 'I');
    }

}
