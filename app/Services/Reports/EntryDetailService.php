<?php

namespace App\Services\Reports;
use App\Models\Designation;
use App\Models\ReportApplicationStatement;
use App\Services\HelpService\PDFService;

class EntryDetailService
{
    public function entryDetail($designation,$order_number)
    {
        $search = Designation::where('designation', $designation)->first();

        $data =  ReportApplicationStatement
            ::where('order_number',$order_number)
            ->with('designation','designationEntry','designationMaterial.material')
            ->get();

        $res = collect();

        $this->findDetailsInNode($search->id, $data, $res,0);

        $res = $res->sortBy([
            ['type', 'asc'],
            ['designationEntry', 'asc'],
            ['category_code', 'asc'],
        ]);
        $resGrouped = $res->groupBy('designation');

        $pdf_path = $this->getPdf($resGrouped,$order_number);

        return $pdf_path;
    }

    public function getPdf($resGrouped,$order_number)
    {
        $width = array(40,30,50,70,60,20,7);

        $header1 = [ 'Номер вузла',
            "Назва деталі",
            'Номер вузла',
            'Назва деталі',
            'Матеріал',
            'Норма',
            'К-ть'
        ];
        $header2 = [ '(КУДИ)',
            "(КУДИ)",
            '(ЩО)',
            '(ЩО)',
            '',
            '',
            ''
        ];
        $pdf = PDFService::getPdf($header1,$header2,$width,'Комплектовочна відомість','Заказ №'.$order_number);
        $page = 2;
        $height = 10;
        $max_height = 10;
        //dd($data);
        foreach ($resGrouped as $designation => $group) {

            if($pdf->getY() >= 185) {
                $pdf->Cell(0, 5, 'ЛИСТ '.$page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                $pdf = PDFService::getHeaderPdf($pdf, $header1, $header2, $width);
                $page++;
            }
            // Добавление названия детали
            $pdf->MultiCell($width[0], $height, $designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $max_height, 'T');

            // Добавление деталей
            foreach ($group as $key=>$row) {

                if($key == 0){
                    $pdf->MultiCell($width[1], $height, $row['designation_name'], 0, 'L', 0, 0, '', '', true, 0, false, true, $max_height, 'T');
                    $pdf->Ln();
                }
                if($pdf->getY() >= 185) {
                    $pdf->Cell(0, 5, 'ЛИСТ '.$page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                    $pdf = PDFService::getHeaderPdf($pdf, $header1, $header2, $width);
                    $page++;
                }

                $pdf->MultiCell($width[0], $height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $max_height, 'T');
                $pdf->MultiCell($width[1], $height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $max_height, 'T');
                $pdf->MultiCell($width[2], $height, $row['designationEntry'], 0, 'L', 0, 0, '', '', true, 0, false, true, $max_height, 'T');
                $pdf->MultiCell($width[3], $height, $row['designationEntry_name'], 0, 'L', 0, 0, '', '', true, 0, false, true, $max_height, 'T');
                if($row['material']->isNotEmpty()) {
                    $first = 0;
                    foreach ($row['material'] as $material) {
                        $first++;
                        if($first>1){
                            $pdf->Ln();
                            $pdf->MultiCell($width[0], $height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $max_height, 'T');
                            $pdf->MultiCell($width[1], $height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $max_height, 'T');
                            $pdf->MultiCell($width[2], $height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $max_height, 'T');
                            $pdf->MultiCell($width[3], $height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $max_height, 'T');

                        }
                        $pdf->MultiCell($width[4], $height, $material->material->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $max_height, 'T');
                        $pdf->MultiCell($width[5], $height, $material->material->norm, 0, 'L', 0, 0, '', '', true, 0, false, true, $max_height, 'T');
                        if($first==1 ){
                            $pdf->MultiCell($width[6], $height, $row['quantity'], 0, 'L', 0, 0, '', '', true, 0, false, true, $max_height, 'T');
                        }

                    }
                }else{
                    $pdf->MultiCell($width[4], $height,'', 0, 'L', 0, 0, '', '', true, 0, false, true, $max_height, 'T');
                    $pdf->MultiCell($width[5], $height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $max_height, 'T');

                }
                $pdf->Ln();
            }
        }
        $pdf_path = storage_path('app/public/entry_detail_order_'.$order_number.'.pdf');
        $pdf->Output($pdf_path, 'F');
        $pdf->Output($pdf_path, 'I');

    }
    function findDetailsInNode($searchID, $data, &$res, $type) {

        // Ищем все designation_entry_id связанные с текущим designation_id
        foreach ($data as $row) {
            if ($row['designation_id'] == $searchID) {
               $res->push(collect([
                    'id' => $row->id,
                    'designation_id' => $row->designation_id,
                    'designation' => $row->designation->designation,
                    'designation_name' => $row->designation->name,
                    'designationEntry' => $row->designationEntry->designation??"",
                    'designationEntry_name' => $row->designationEntry->name??"",
                    'material' => $row->designationMaterial,//->material->name??"",
                    'norm' => $row->designationMaterial,//->norm??"",
                    'quantity' => $row->quantity,
                    'type' => $type,
                    'category_code' => $row->category_code
                ]));
                if($row['designation_id'] != $row['designation_entry_id'] &&  $row->designationEntry){
                    if($res->where('designation_id',$row['designation_entry_id'])->isEmpty()){
                        $this->findDetailsInNode($row['designation_entry_id'], $data,$res,$row->designationEntry->designation);
                    }
                }
            }
        }
    }
}
