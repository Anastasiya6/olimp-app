<?php

namespace App\Services\Reports;
use App\Models\ReportApplicationStatement;
use App\Repositories\Interfaces\OrderNameRepositoryInterface;
use App\Services\HelpService\PDFService;

class NotNormForMaterialService
{

    public $order_name_id;

    private $orderNameRepository;

    public function __construct(OrderNameRepositoryInterface $orderNameRepository)
    {
        $this->orderNameRepository = $orderNameRepository;
    }

    public function notNormForMaterial($department,$order_name_id)
    {
        $this->order_name_id = $order_name_id;

        $items = ReportApplicationStatement
            ::where('order_name_id',$order_name_id)
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
        $order_number = $this->orderNameRepository->getByOrderFirst($this->order_name_id);
        $pdf = PDFService::getPdf($header1,$header2,$width,'Відсутні норми',' Цех '.$department.' Замовлення №'.$order_number->name);
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

                $pdf->Ln();
            }
        }
        $pdf->Output('notNormMaterial.pdf', 'I');

    }

}
