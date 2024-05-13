<?php

namespace App\Services\Reports;
use App\Models\Specification;
use App\Services\HelpService\PDFService;

class EntryDetailDesignationService
{
    public $height = 10;

    public $max_height = 10;

    public $width = array(40,40,50,55,45,20,12,30);

    public $header1 = [ 'Номер вузла',
                        "Назва деталі",
                        'Номер вузла',
                        'Назва деталі',
                        'Матеріал',
                        'Норма',
                        'К-ть',
                        'Норма',
                       // ''
                    ];
    public $header2 = [ '(КУДИ)',
                        "(КУДИ)",
                        '(ЩО)',
                        '(ЩО)',
                        '',
                        '',
                        '',
                        'на застос.',
                        //''
                        ];
    public $page = 2;

    public $pdf = null;

    public function entryDetailDesignation($designation)
    {
        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'Комплектовочна відомість','Деталь '.$designation->designation);

        // Добавление названия детали
        $this->pdf->MultiCell($this->width[0], $this->height, $designation->designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
        $this->pdf->MultiCell($this->width[1], $this->height, $designation->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
        $not_first_material = 0;
        if($designation->designationMaterial->isNotEmpty()) {
            foreach ($designation->designationMaterial as $material) {
                if($not_first_material == 1){
                    $this->pdf->MultiCell($this->width[0], $this->height,'', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                    $this->pdf->MultiCell($this->width[1], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                }
                $this->pdf->MultiCell($this->width[2], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                $this->pdf->MultiCell($this->width[3], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                $this->pdf->MultiCell($this->width[4], $this->height, $material->material->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                $this->pdf->MultiCell($this->width[5], $this->height, $material->norm . " х", 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                $this->pdf->MultiCell($this->width[6], $this->height, "1", 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                $this->pdf->MultiCell($this->width[7], $this->height, "=".$material->norm, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                $this->pdf->Ln();
                $not_first_material++;
            }
        }else{
            $this->pdf->Ln();
        }
        $this->node($designation->id,0,1);

        $pdf_path = storage_path('app/public/entry_detail_'.$designation->designation.'.pdf');
        $this->pdf->Output($pdf_path, 'F');
        $this->pdf->Output($pdf_path, 'I');
    }

    public function node($designation_id,$level,$quantity_node,$designation='',$designation_name='',$pred_quantity_node=0)
    {
        $this->newList();

        $specifications = Specification
            ::where('designation_id', $designation_id)
            ->with(['designationEntry','designationMaterial'])
            ->get();

        //$specifications = $specifications->sortBy('category_code');
        $specifications = $specifications->sortBy('detail');
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

                $str = $pred_quantity_node ? $quantity_node.' x '.$pred_quantity_node:$quantity_node;

                if ($specification->designationMaterial->isNotEmpty()) {
                    $count = 0;

                    foreach ($specification->designationMaterial as $material) {
                        $count++;
                        $total = $pred_quantity_node ? $quantity_node * $specification->quantity * $material->norm * $pred_quantity_node : $quantity_node * $specification->quantity * $material->norm;
                        if($count>1){
                            $this->pdf->MultiCell($this->width[0], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->MultiCell($this->width[1], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->MultiCell($this->width[2], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->MultiCell($this->width[3], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                            $this->pdf->MultiCell($this->width[4], $this->height, $material->material->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->MultiCell($this->width[5], $this->height, $material->norm." х", 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->MultiCell($this->width[6], $this->height, $specification->quantity." х", 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->MultiCell($this->width[7], $this->height, $str."=".$total, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            //$this->pdf->MultiCell($this->width[8], $this->height, $pred_quantity_node, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->Ln();
                        }else{
                            $this->pdf->MultiCell($this->width[4], $this->height, $material->material->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->MultiCell($this->width[5], $this->height, $material->norm." х", 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->MultiCell($this->width[6], $this->height, $specification->quantity." х", 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->MultiCell($this->width[7], $this->height, $str."=".$total, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                           // $this->pdf->MultiCell($this->width[8], $this->height, $pred_quantity_node, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->Ln();
                        }

                    }
                } else {
                    $total = $pred_quantity_node ? $quantity_node * $specification->quantity * $pred_quantity_node : $quantity_node * $specification->quantity;

                    $this->pdf->MultiCell($this->width[4], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                    $this->pdf->MultiCell($this->width[5], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                    $this->pdf->MultiCell($this->width[6], $this->height, $specification->quantity." х", 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                    $this->pdf->MultiCell($this->width[7], $this->height, $str."=".$total, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                   // $this->pdf->MultiCell($this->width[8], $this->height, $pred_quantity_node, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                    $this->pdf->Ln();
                }
                $this->node($specification->designation_entry_id, 1, $specification->quantity,$specification->designationEntry->designation, $specification->designationEntry->name,$quantity_node);

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
