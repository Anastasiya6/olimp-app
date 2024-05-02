<?php

namespace App\Services\Reports;
use App\Models\Designation;
use App\Models\ReportApplicationStatement;
use App\Models\Specification;
use App\Services\HelpService\PDFService;

class EntryDetailDesignationService
{

    public $height = 10;

    public $max_height = 10;

    public $width = array(40,40,50,50,50,20,7,25);

    public $header1 = [ 'Номер вузла',
                        "Назва деталі",
                        'Номер вузла',
                        'Назва деталі',
                        'Матеріал',
                        'Норма',
                        'К-ть',
                        ''
                    ];
    public $header2 = [ '(КУДИ)',
                        "(КУДИ)",
                        '(ЩО)',
                        '(ЩО)',
                        '',
                        '',
                        '',
                        ''
                        ];
    public $page = 2;

    public $pdf = null;

    public function entryDetailDesignation($designation)
    {
        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'Комплектовочна відомість','Деталь '.$designation->designation);

        // Добавление названия детали
        $this->pdf->MultiCell($this->width[0], $this->height, $designation->designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
        $this->pdf->MultiCell($this->width[1], $this->height, $designation->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
        $this->pdf->Ln();
        $this->node($designation->id,0,1);

        $pdf_path = storage_path('app/public/entry_detail_'.$designation->designation.'.pdf');
        $this->pdf->Output($pdf_path, 'F');
        $this->pdf->Output($pdf_path, 'I');
    }

    public function node($designation_id,$level,$quantity_node,$designation='',$designation_name='')
    {
        $this->newList();

        $specifications = Specification
            ::where('designation_id', $designation_id)
           /* ->orderByRaw("
                        CASE
                            WHEN designation LIKE 'КР%' THEN 1
                            WHEN designation LIKE 'ПИ0%' THEN 2
                            ELSE 0
                        END, designation
                    ")*/
            ->with(['designationEntry','designationMaterial'])
            ->get();

        // Проверяем, что коллекция не пуста
        if ($specifications->isNotEmpty()) {

            if ($level == 1) {
                $this->pdf->MultiCell($this->width[0], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                $this->pdf->MultiCell($this->width[1], $this->height, $designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                $this->pdf->MultiCell($this->width[2], $this->height, $designation_name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                $this->pdf->Ln();
            }

            foreach ($specifications as $specification) {

                $this->pdf->MultiCell($this->width[0], $this->height, $designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                $this->pdf->MultiCell($this->width[1], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                $this->pdf->MultiCell($this->width[2], $this->height, $specification->designationEntry->designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                $this->pdf->MultiCell($this->width[3], $this->height, $specification->designationEntry->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                if ($specification->designationMaterial->isNotEmpty()) {
                    $count = 0;
                    foreach ($specification->designationMaterial as $material) {
                        $count++;
                        if($count>1){
                            $this->pdf->MultiCell($this->width[0], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->MultiCell($this->width[1], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->MultiCell($this->width[2], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->MultiCell($this->width[3], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                            $this->pdf->MultiCell($this->width[4], $this->height, $material->material->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->MultiCell($this->width[5], $this->height, $material->norm, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->MultiCell($this->width[6], $this->height, $specification->quantity, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->MultiCell($this->width[7], $this->height, "$quantity_node"."х".$specification->quantity."=".$quantity_node * $specification->quantity * $material->norm, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->Ln();
                        }else{
                            $this->pdf->MultiCell($this->width[4], $this->height, $material->material->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->MultiCell($this->width[5], $this->height, $material->norm, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->MultiCell($this->width[6], $this->height, $specification->quantity, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->MultiCell($this->width[7], $this->height, "$quantity_node"."х".$specification->quantity."=".$quantity_node * $specification->quantity * $material->norm, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->Ln();
                        }

                    }
                } else {
                    $this->pdf->MultiCell($this->width[4], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                    $this->pdf->MultiCell($this->width[5], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                    $this->pdf->MultiCell($this->width[6], $this->height, $specification->quantity, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                    $this->pdf->Ln();
                }
                $this->node($specification->designation_entry_id, 1, $specification->quantity,$specification->designationEntry->designation, $specification->designationEntry->name);

            }
        }
    }

    public function newList()
    {
        if ($this->pdf->getY() >= 185) {
            $this->pdf->Cell(0, 5, 'ЛИСТ ' . $this->page, 0, 1, 'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
            $this->pdf = PDFService::getHeaderPdf($this->pdf, $this->header1, $this->header2, $this->width);
            $this->page++;
        }
    }
}
