<?php

namespace App\Services\Reports;
use App\Models\Specification;
use App\Services\HelpService\PDFService;
use App\Services\HelpService\SpecificationService;
use App\Services\HelpService\StatementService;

class EntryDetailDesignationService
{
    public $height = 10;

    public $max_height = 10;

    /* відділ по якому потрібно переглянути інфо*/
    public $department;

    public $designation;

    public $width = array(40,40,43,54,45,8,20,12,30);

    public $department_str;

    public $header1 = [ 'Номер вузла',
        "Назва деталі",
        'Номер вузла',
        'Назва деталі',
        'Матеріал',
        'Од.',
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
        'вим.',
        '',
        '',
        'на застос.',
        //''
    ];
    public $page = 2;

    public $pdf = null;

    public function entryDetailDesignation($designation,$department)
    {
        $this->department = $department;

        $this->designation = $designation;

        $this->department_str = $this->department==0?'Всі цеха':'Цех '.$this->department;

        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'Комплектовочна відомість','Деталь '.$designation->designation.' '.$this->department_str);

        // Добавление названия детали
        $this->pdf->MultiCell($this->width[0], $this->height, $designation->designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
        $this->pdf->MultiCell($this->width[1], $this->height, $designation->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

        /*Інфо по матеріалам по головному вузлу*/

        $this->getInfoMaterial();

        $this->node($designation->id,0,1);

        $this->addFooter();

        $pdf_path = storage_path('app/public/entry_detail_'.$designation->designation.'.pdf');
        // $this->pdf->Output($pdf_path, 'F');
        $this->pdf->Output($pdf_path, 'I');
    }

    public function addFooter(){

        //$this->pdf->Ln();
        $this->pdf->MultiCell($this->width[0], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
        $this->pdf->MultiCell($this->width[1], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
        $this->pdf->MultiCell($this->width[2], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
        $this->pdf->MultiCell($this->width[3], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

        $this->pdf->MultiCell($this->width[4], $this->height, 'ТЕХНОЛОГ ЦЕХУ', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

        $this->pdf->MultiCell($this->width[5], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

        $this->pdf->Ln();

        $this->pdf->MultiCell($this->width[0], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
        $this->pdf->MultiCell($this->width[1], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
        $this->pdf->MultiCell($this->width[2], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
        $this->pdf->MultiCell($this->width[3], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');


        $this->pdf->MultiCell($this->width[4], $this->height, 'НАЧАЛЬНИК ЦЕХУ', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

        $this->pdf->MultiCell($this->width[5], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

    }

    public function addEmptyString($count){

        for($i=1;$i<$count;$i++) {

            $this->pdf->MultiCell($this->width[0], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[1], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->Ln();
        }
    }

    public function node($designation_id,$level,$quantity_node,$route=0,$tm_last=0,$designation='',$designation_name='',$pred_quantity_node=0)
    {
        $specifications = Specification
            ::where('designation_id', $designation_id)
            ->with(['designations','designationEntry.unit','designationMaterial.material.unit'])
            ->get();

        //$specifications = $specifications->sortBy('category_code');
        $specifications = $specifications->sortBy('detail');

        // Проверяем, что коллекция не пуста
        if ($specifications->isNotEmpty()) {

            /* 0 - відображаємо інфо для всіх цехів*/
            if($route == 0 || $route == $this->department) {
                if ($level == 1) {
                    $this->pdf->MultiCell($this->width[0], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                    $this->pdf->MultiCell($this->width[1], $this->height, $designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                    $this->pdf->MultiCell($this->width[2], $this->height, $designation_name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                    $this->pdf->Ln();
                }
            }
            foreach ($specifications as $specification) {

                $tm = StatementService::getTm($specification);

                $route = SpecificationService::getRoute($specification,$tm,$this->department);

                if($route == 0 || $route == $this->department) {

                    $this->pdf->MultiCell($this->width[0], $this->height, $designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                    $this->pdf->MultiCell($this->width[1], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                    $this->pdf->MultiCell($this->width[2], $this->height, $specification->designationEntry->designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                    $this->pdf->MultiCell($this->width[3], $this->height, $specification->designationEntry->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                    $str = $pred_quantity_node ? $quantity_node . ' x ' . $pred_quantity_node : $quantity_node;

                    $route_designationEntry = substr($specification->designationEntry->route,0,2);

                    if ($specification->designationMaterial->isNotEmpty()) {

                        $count = 0;
                        if($route_designationEntry != $this->department && $this->department != 0){

                            $this->pdf->MultiCell($this->width[4], $this->height, $route_designationEntry.' цех', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                            $this->pdf->MultiCell($this->width[5], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->MultiCell($this->width[6], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->MultiCell($this->width[7], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                            $this->pdf->Ln();

                        }else{
                            foreach ($specification->designationMaterial as $material) {
                                $count++;
                                $total = $pred_quantity_node ? $quantity_node * $specification->quantity * $material->norm * $pred_quantity_node : $quantity_node * $specification->quantity * $material->norm;
                                if ($count > 1) {
                                    $this->newList();
                                    $this->pdf->MultiCell($this->width[0], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                                    $this->pdf->MultiCell($this->width[1], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                                    $this->pdf->MultiCell($this->width[2], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                                    $this->pdf->MultiCell($this->width[3], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                                    $this->pdf->MultiCell($this->width[4], $this->height, $material->material->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                                    $this->pdf->MultiCell($this->width[5], $this->height, $material->material->unit->unit, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                                    $this->pdf->MultiCell($this->width[6], $this->height, $material->norm . " х", 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                                    $this->pdf->MultiCell($this->width[7], $this->height, $specification->quantity . " х", 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                                    $this->pdf->MultiCell($this->width[8], $this->height, $str . "=" . $total, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                                    $this->pdf->Ln();
                                } else {
                                    $this->pdf->MultiCell($this->width[4], $this->height, $material->material->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                                    $this->pdf->MultiCell($this->width[5], $this->height, $material->material->unit->unit, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                                    $this->pdf->MultiCell($this->width[6], $this->height, $material->norm . " х", 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                                    $this->pdf->MultiCell($this->width[7], $this->height, $specification->quantity . " х", 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                                    $this->pdf->MultiCell($this->width[8], $this->height, $str . "=" . $total, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                                    $this->pdf->Ln();
                                }
                            }
                        }
                    } else {
                        $unit = str_starts_with($specification->designationEntry->designation, 'КР') ? 'шт' : $specification->designationEntry->unit->unit??'';
                        $total = $pred_quantity_node ? $quantity_node * $specification->quantity * $pred_quantity_node : $quantity_node * $specification->quantity;
                        $this->pdf->MultiCell($this->width[4], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                        $this->pdf->MultiCell($this->width[5], $this->height, $unit, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                        $this->pdf->MultiCell($this->width[6], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                        $this->pdf->MultiCell($this->width[7], $this->height, $specification->quantity . " х", 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                        $this->pdf->MultiCell($this->width[8], $this->height, $str . "=" . $total, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                        $this->pdf->Ln();
                    }
                }
                $this->newList();

                $this->node($specification->designation_entry_id, 1, $specification->quantity, $route, $tm, $specification->designationEntry->designation, $specification->designationEntry->name, $quantity_node);

            }
        }
    }

    public function getInfoMaterial()
    {
        $not_first_material = 0;

        if($this->designation->designationMaterial->isNotEmpty()) {
            foreach ($this->designation->designationMaterial as $material) {
                if($not_first_material == 1){
                    $this->pdf->MultiCell($this->width[0], $this->height,'', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                    $this->pdf->MultiCell($this->width[1], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                }
                $this->pdf->MultiCell($this->width[2], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                $this->pdf->MultiCell($this->width[3], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                $this->pdf->MultiCell($this->width[4], $this->height, $material->material->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                $this->pdf->MultiCell($this->width[5], $this->height, $material->material->unit->unit, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                $this->pdf->MultiCell($this->width[6], $this->height, $material->norm . " х", 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                $this->pdf->MultiCell($this->width[7], $this->height, "1", 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                $this->pdf->MultiCell($this->width[8], $this->height, "=".$material->norm, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                $this->pdf->Ln();
                $not_first_material++;
            }
        }else{
            $this->pdf->Ln();
        }
    }

    public function newList()
    {
        if ($this->pdf->getY() >= 185) {
            $this->pdf->Cell(0, 5, $this->department_str.' '.'ЛИСТ ' . $this->page, 0, 1, 'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
            $this->pdf = PDFService::getHeaderPdf($this->pdf, $this->header1, $this->header2, $this->width);
            $this->page++;
        }
    }
}
