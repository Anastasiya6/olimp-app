<?php

namespace App\Services\Reports;
use App\Models\ReportApplicationStatement;
use App\Services\HelpService\PDFService;

class NotNormForMaterialService
{
    public function notNormForMaterial($department,$order_number)
    {
        $items = ReportApplicationStatement
            ::where('order_number',$order_number)
            //->select('designation_entry_id','order_designationEntry','designation_id')
            //->groupBy('designation_entry_id','order_designationEntry','designation_id')
            ->doesntHave('designationMaterial')
            ->where('category_code','!=','0')
            ->with('designation')
            ->with(['designationEntry' => function ($query) use($department) {
                $query
                    ->distinct()
                    ->whereRaw("SUBSTRING(route, 1, 2) = '$department'")
                    ->where('designation','NOT LIKE', 'КР%')
                    ->where('designation','NOT LIKE', 'ПИ0%');

            }])
            ->orderBy('order_designation')
            ->get();

        $width = array(50,100,50,100);
        $header1 = [ 'Номер вузла/деталі',
            "Назва деталі",
            'Номер вузла/деталі(КУДИ)',
            "Назва деталі(КУДИ)",
        ];
        $header2 = [ '',
            '',
            '',
            ''
        ];
        $pdf = PDFService::getPdf($header1,$header2,$width,'Відсутні норми',' Цех '.$department.' Заказ '.$order_number);
        $page = 2;
        $height = 10;
        $max_height = 10;
        //dd($items);
        foreach ($items as $row) {
           // dd($row);
            if($pdf->getY() >= 185) {
               // $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                $pdf->Cell(0, 5, 'ЛИСТ '.$page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                $pdf = PDFService::getHeaderPdf($pdf, $header1, $header2, $width);
                $page++;
            }
            if($row['designationEntry']){
                $pdf->MultiCell($width[0], $height, $row['designationEntry']->designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $max_height, 'B');
                $pdf->MultiCell($width[1], $height, $row['designationEntry']->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $max_height, 'B');
                $pdf->MultiCell($width[2], $height, $row['designation']->designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $max_height, 'B');
                $pdf->MultiCell($width[3], $height, $row['designation']->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $max_height, 'B');

                /*$pdf->Cell($width[0], 40, $row['designationEntry']->designation);
                $pdf->Cell($width[1], 40, $row['designationEntry']->name);
                $pdf->Cell($width[2], 40, $row['designation']->designation);
                $pdf->Cell($width[3], 40, $row['designation']->name);*/
                $pdf->Ln();
            }
        }
        $pdf->Output('notNormMaterial.pdf', 'I');

    }

}
