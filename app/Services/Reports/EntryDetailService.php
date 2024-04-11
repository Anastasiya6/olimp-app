<?php

namespace App\Services\Reports;
use App\Models\Designation;
use App\Models\ReportApplicationStatement;
use App\Services\HelpService\PDFService;
use Illuminate\Contracts\Queue\Job;
use Ramsey\Collection\Collection;

class EntryDetailService
{
    public function entryDetail($designation)
    {
        $search = Designation::where('designation', $designation)->first();
       // dd($search);
        $data =  ReportApplicationStatement
            ::with('designation','designationEntry','designationMaterial.material')
            ->get();

        $res = collect();

        $this->findDetailsInNode($search->id, $data, $res,0);

        $res = $res->sortBy([
            ['type', 'asc'],
            ['designationEntry', 'asc'],
            ['category_code', 'asc'],
        ]);
        $resGrouped = $res->groupBy('designation');
        //dd($res);
        $width = array(40,30,50,80,60,7);

        $header1 = [ 'Номер вузла',
            "Назва деталі",
            'Номер вузла',
            'Назва деталі',
            'Матеріал',
            'К-ть'
        ];
        $header2 = [ '(КУДИ)',
            "(КУДИ)",
            '(ЩО)',
            '(ЩО)',
            '',
            ''
        ];
        $pdf = PDFService::getPdf($header1,$header2,$width,'');
        $page = 2;
        //dd($data);
        foreach ($resGrouped as $designation => $group) {

            if($pdf->getY() >= 185) {
                $pdf->Cell(0, 5, 'ЛИСТ '.$page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                $pdf = PDFService::getHeaderPdf($pdf, $header1, $header2, $width);
                $page++;
            }
            // Добавление названия детали
            $pdf->Cell($width[0], 10, $designation); // Название детали

            // Добавление деталей
            foreach ($group as $key=>$row) {

                if($key == 0){
                    $pdf->Cell($width[1], 10, $row['designation_name']); // Название детали
                    $pdf->Ln();
                }
                if($pdf->getY() >= 185) {
                    $pdf->Cell(0, 5, 'ЛИСТ '.$page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                    $pdf = PDFService::getHeaderPdf($pdf, $header1, $header2, $width);
                    $page++;
                }
                $pdf->Cell($width[0], 10, '');
                $pdf->Cell($width[1], 10, '');
                $pdf->Cell($width[2], 10, $row['designationEntry']);
                $pdf->Cell($width[3], 10, $row['designationEntry_name']);
                $pdf->Cell($width[4], 10, $row['material']);
                $pdf->Cell($width[5], 10, $row['quantity']);
                $pdf->Ln();
            }
        }

        $pdf->Output('entry_detail.pdf', 'I');

    }

    function findDetailsInNode($searchID, $data, &$res, $type) {

        // Ищем все designation_entry_id связанные с текущим designation_id
        foreach ($data as $row) {
            if ($row['designation_id'] == $searchID) {
               $res->push(collect([
                    'id' => $row->id,
                    'designation' => $row->designation->designation,
                    'designation_name' => $row->designation->name,
                    'designationEntry' => $row->designationEntry->designation??"",
                    'designationEntry_name' => $row->designationEntry->name??"",
                    'material' => $row->designationMaterial->material->name??"",
                    'quantity' => $row->quantity,
                    'type' => $type,
                    'category_code' => $row->category_code
                ]));
                if($row['designation_id'] != $row['designation_entry_id'] &&  $row->designationEntry){
                    $this->findDetailsInNode($row['designation_entry_id'], $data,$res,$row->designationEntry->designation);
                }
            }
        }
    }
}
