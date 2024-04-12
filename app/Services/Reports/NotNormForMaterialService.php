<?php

namespace App\Services\Reports;
use App\Models\Designation;
use App\Models\ReportApplicationStatement;
use App\Services\HelpService\PDFService;
use Illuminate\Contracts\Queue\Job;
use Ramsey\Collection\Collection;

class NotNormForMaterialService
{
    public function notNormForMaterial($department)
    {
        $items = ReportApplicationStatement
            ::select('designation_entry_id','order_designationEntry')
            ->groupBy('designation_entry_id','order_designationEntry')
            ->doesntHave('designationMaterial')
            ->where('category_code','!=','0')
            ->with(['designationEntry' => function ($query) use($department) {
                $query
                    ->whereRaw("SUBSTRING(route, 1, 2) = '$department'")
                    ->where('designation','NOT LIKE', 'КР%')
                    ->where('designation','NOT LIKE', 'ПИ0%');

            }])
            ->orderBy('order_designationEntry')
            ->get();

        $width = array(50,70);
        $header1 = [ 'Номер вузла/деталі',
            "Назва деталі",
        ];
        $header2 = [ '',
            '',
        ];
        $pdf = PDFService::getPdf($header1,$header2,$width,'');

        $page = 2;

        foreach ($items as $row) {

            if($pdf->getY() >= 185) {
                $pdf->Cell(0, 5, 'ЛИСТ '.$page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                $pdf = PDFService::getHeaderPdf($pdf, $header1, $header2, $width);
                $page++;
            }
            if($row['designationEntry']){
                $pdf->Cell($width[0], 10, $row['designationEntry']->designation);
                $pdf->Cell($width[1], 10, $row['designationEntry']->name);
                $pdf->Ln();
            }
        }
        $pdf->Output('notNormMaterial.pdf', 'I');

    }

}
