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
            /*->whereHas('designation', function ($query) use ($department){
            $query-> whereRaw("SUBSTRING(route, 1, 2) = '$department'");
        })*/
            ->has('designationMaterial.material')
            ->with('designationEntry','designationMaterial.material')
            ->orderBy('order_designationEntry_letters')
            ->orderBy('order_designationEntry')
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

        $groupedData = $data->groupBy('id')->map(function ($group) {
            // Внутри каждой группы по материалу, группируем по detail_name
            return $group->groupBy('detail_name')->map(function ($details) {
                return [
                    'id' => $details->first()['id'], // ID материала из первого элемента
                    'material_name' => $details->first()['material_name'], // Имя материала из первого элемента
                    'detail_name' => $details->first()['detail_name'], // Наименование детали
                    'quantity_total' => $details->sum('quantity_total'), // Сумма quantity_total
                    'unit' => $details->first()['unit'], // Единица измерения из первого элемента
                    'norm' => $details->first()['norm'], // Сумма норм для группы
                ];
            });
        });
     
        return $groupedData;
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
        //dd($data);
        foreach ($data as $row) {
                
            if($pdf->getY() >= 185) {
                $pdf->Cell(0, 5, 'ЛИСТ '.$page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                $pdf = PDFService::getHeaderPdf($pdf, $header1, $header2, $width);
                $page++;
            }

            $key = 0;
            foreach($row as $detail){
                if($key == 0){
                    $pdf->Cell(100, 10, $detail['material_name']);
                    $pdf->Ln();
                }
                $key++;
                $pdf->Cell($width[0], 10, '');
                $pdf->Cell($width[1], 10, $detail['detail_name']);
                $pdf->Cell($width[2], 10, $detail['quantity_total']);
                $pdf->Cell($width[3], 10, $detail['unit']);
                $pdf->Cell($width[4], 10, $detail['norm']);
                $pdf->Cell($width[5], 10, $detail['norm']*$detail['quantity_total']);
                $pdf->Ln();
                $sum_norm = $sum_norm + $detail['norm']*$detail['quantity_total'];
           }
            $pdf->Cell(270, 10, 'Разом по матер. '.$sum_norm,0,1,'R');
            $pdf->Ln();
            $sum_norm = 0;
            
        }
     
        // Выводим PDF в браузер
        $pdf->Output('example.pdf', 'I');
    }

}
