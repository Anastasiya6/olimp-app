<?php

namespace App\Services\Reports;
use App\Models\Task;
use App\Services\HelpService\MaterialService;
use App\Services\HelpService\PDFService;

class TaskService
{
    public $width = array(35,50,10,15,90,10,45,30);

    public $width1 = array(16,50,100,30);

    public $height = 10;

    public $max_height = 10;
    // Заголовок таблицы
    public $header1 = ['Номер',
                        'Назва',
                        'К-ть',
                        'Код 1C',
                        'Матеріал',
                        'Од',
                        'Норма',
                        'Норма*коеф.'];
    public $header2 = [ 'деталі',
                        'деталі',
                        '',
                        '',
                        '',
                        '.вим.',
                        '',
                        ''];
    public $header3 = [
        'Номер',
        'Номер деталі',
        'Назва деталі',
        'Кількість'
  ];
    public $header4 = [
        'докумен.',
        '',
        '',
        ''
 ];
    public $pdf = null;

    public $page = 2;

    public $sender_department_id;

    public $records;

    public $materialService;

    public $type_report = 0;

    public $ids;

    public function __construct( MaterialService $service )
    {
        $this->materialService = $service;
    }

    public function task($ids,$sender_department_id,$type_report = 0)
    {
        $this->ids = $ids;

        $this->type_report = $type_report;

        $this->sender_department_id = $sender_department_id;

        $records = $this->getRecords();

        /*Report by detail-specification*/
        if($this->type_report == 0) {
            //dd(0);
            $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'ПЕРЕЛІК ДЕТАЛЕЙ ',' ');

            $this->getDetailSpecificationPdf($records);

        /*Report together by materials*/
        }elseif($this->type_report == 1){

            $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'ПЕРЕЛІК ДЕТАЛЕЙ','');

            $this->getMaterialPdf($records);

            /*Report by details*/
        }elseif($this->type_report == 2){
            dd('ss');

            $this->pdf = PDFService::getPdf($this->header3,$this->header4,$this->width1,'ПЕРЕЛІК ДЕТАЛЕЙ ',' ','P');

            $this->getDetailPdf($records);

        }
    }

    private function getDetailPdf($records)
    {
        foreach ($records as $item) {

            $this->setNewList($this->header3,$this->header4,$this->width1,270);

            $this->pdf->MultiCell($this->width1[0], $this->height, $item->document_number, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width1[1], $this->height, $item->designation->designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width1[2], $this->height, $item->designation->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width1[3], $this->height, $item->quantity, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->Ln();
        }

        // Выводим PDF в браузер
        $this->pdf->Output('task_no_detail_pdf', 'I');
    }

    private function getDetailSpecificationPdf($records)
    {
        foreach ($records as $item) {

            $this->setNewList($this->header1,$this->header2,$this->width);

            $this->pdf->MultiCell($this->width[0], $this->height, $item->designation->designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[1], $this->height, $item->designation->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[2], $this->height, $item->quantity, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[3], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $first = 0;

            if($item->materials) {

                $materials = $item->materials->sortBy('material')->sortBy('sort');

                foreach ($materials as $norm) {
                    $column = 3;
                    $first++;

                    $this->setNewList($this->header1,$this->header2,$this->width);

                    if ($first > 1) {

                        $this->pdf->Ln();

                        for($i=0;$i<=$column;$i++) {
                            $this->pdf->MultiCell($this->width[$i], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                        }

                    }

                    list($multiplier_str, $multiplier) = $this->materialService->getTypeMaterial($norm->type,$norm->material);

                    $this->pdf->MultiCell($this->width[++$column], $this->height, $norm->material, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                    $this->pdf->MultiCell($this->width[++$column], $this->height, $norm->unit, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                    $this->pdf->MultiCell($this->width[++$column], $this->height, $norm->sort == 0 ? $norm->norm .' * '.$norm->quantity.' * '. $item->quantity. $multiplier_str . ' = ' : $norm->norm .' * '. $item->quantity . ' = ', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                    $this->pdf->MultiCell($this->width[++$column], $this->height, $norm->sort == 0 ? $norm->norm * $norm->quantity * $item->quantity * $multiplier : $norm->norm * $item->quantity, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                }
            }

            $this->pdf->Ln();
        }

        // Выводим PDF в браузер
        $this->pdf->Output('task_detail_specification'.'.pdf', 'I');
    }

    private function setNewList($header1, $header2,$width,$height=180)
    {
        if($this->pdf->getY() >= $height) {
            $this->pdf->Ln();
            $this->pdf->Cell(0, 10, 'ЛИСТ '.$this->page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
            $this->pdf = PDFService::getHeaderPdf($this->pdf, $header1, $header2, $width);
            $this->page++;
        }
    }

    private function getRecords()
    {
        $records = Task
            ::whereIn('id',$this->ids)
            ->with('designationMaterial.material','designationMaterial.designation')
            ->get();

        return $this->getMaterials($records);
    }

    private function getMaterials($records)
    {
        return $this->materialService->material($records,$this->type_report,$this->sender_department_id,'material_id');

    }

    private function getMaterialPdf($materials)
    {
        $this->pdf->SetFont('dejavusans', 'B', 14);

        $this->pdf->Cell(0, 10, "Разом по матеріалам",0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку

        $this->pdf->SetFont('dejavusans', '', 10);

        foreach ($materials as $item) {

            $this->setNewList($this->header1,$this->header2,$this->width);
            //dd($item['type']);
            list($multiplier_str, $multiplier) = $this->materialService->getTypeMaterial($item['type'],$item['material']);

            $this->pdf->MultiCell($this->width[0], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[1], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[2], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[3], $this->height, $item['code_1c'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[4], $this->height, $item['material'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[5], $this->height,$item['unit'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[6], $this->height, $item['sort'] == 0 ? $item['quantity_norm_quantity_detail']. $multiplier_str .' = ' : $item['quantity_norm_quantity_detail'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[7], $this->height, $item['sort'] == 0 ? $item['quantity_norm_quantity_detail'] * $multiplier : '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->Ln();
        }

        $this->pdf->Output('task_materials_.pdf', 'I');
    }
}
