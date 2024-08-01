<?php

namespace App\Services\Reports;
use App\Models\DeliveryNote;
use App\Models\Specification;
use App\Services\HelpService\PDFService;

class WriteOffService
{
    public $width = array(25,25,40,10,30,30,50,10,30,30);

    public $height = 10;

    public $max_height = 10;
    // Заголовок таблицы
    public $header1 = ['Номер',
                        'Дата',
                        'Номер',
                        'К-ть',
                        'Замовлення №',
                        'Код 1C',
                        'Матеріал',
                        'Од',
                        'Норма',
                        'Норма * 1.2'];
    public $header2 = ['документа',
                        'документа',
                        'деталі',
                        '',
                        '',
                        '',
                        '',
                        '.вим.',
                        '',
                        ''];
    public $pdf = null;

    public $page = 2;

    public $start_date;

    public $end_date;

    public $sender_department_id;

    public $receiver_department_id;

    public $order_number;

    public $ids;

    public $all_materials;

    public function writeOff($ids,$order_number,$start_date,$end_date,$sender_department,$receiver_department)
    {
        $this->ids = json_decode($ids);

        $this->order_number = $order_number;

        $this->start_date = $start_date;

        $this->end_date = $end_date;

        $this->sender_department_id = $sender_department;

        $this->receiver_department_id = $receiver_department;

        $records = $this->getRecords();

        $this->getPdf($records);
    }

    public function getPdf($records)
    {
        //dd($records);
        $start_date_str = \Carbon\Carbon::parse($this->start_date)->format('d.m.Y');

        $end_date_str = \Carbon\Carbon::parse($this->end_date)->format('d.m.Y');

        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'ЗДАТОЧНІ З '.$start_date_str.' ПО '.$end_date_str,' ЗАМОВЛЕННЯ №'.$this->order_number);

        // Добавление данных таблицы
        foreach ($records as $item) {
           // dd($item->designation_number);

            $this->setNewList();
           // $this->pdf->Ln();

            $this->pdf->MultiCell($this->width[0], $this->height, $item->document_number, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[1], $this->height, \Carbon\Carbon::parse($item->document_date)->format('d.m.Y'), 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[2], $this->height, $item->designation->designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[3], $this->height, $item->quantity, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[4], $this->height, $item->order_number, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[4], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $first = 0;

            if($item->materials) {

                $materials = $item->materials->sortBy('material')->sortBy('sort');

                foreach ($materials as $norm) {
                  //  dd($norm);
                    $first++;

                    $this->setNewList();

                    if ($first > 1) {

                        $this->pdf->Ln();

                        $this->pdf->MultiCell($this->width[0], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                        $this->pdf->MultiCell($this->width[1], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                        $this->pdf->MultiCell($this->width[2], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                        $this->pdf->MultiCell($this->width[3], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                        $this->pdf->MultiCell($this->width[4], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                        $this->pdf->MultiCell($this->width[5], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                    }

                    $this->pdf->MultiCell($this->width[6], $this->height, $norm->material, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                    $this->pdf->MultiCell($this->width[7], $this->height, $norm->unit, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                    $this->pdf->MultiCell($this->width[8], $this->height, $norm->sort == 0 ? $norm->norm.' * 1.2 = ' : $norm->norm, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                    $this->pdf->MultiCell($this->width[9], $this->height, $norm->sort == 0 ? $norm->norm * 1.2 : '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                }
            }


            $this->pdf->Ln();
        }
        $this->pdf->SetFont('dejavusans', 'B', 14);
        $this->pdf->Cell(0, 10, "Разом по матеріалам",0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
        $this->pdf->SetFont('dejavusans', '', 10);

        $this->getPdfMaterials($records->materials);

        // Выводим PDF в браузер
        $this->pdf->Output('delivery_note_'.$this->order_number.'.pdf', 'I');
    }

    private function setNewList()
    {
        if($this->pdf->getY() >= 180) {
            $this->pdf->Cell(0, 10, 'ЛИСТ '.$this->page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
            $this->pdf = PDFService::getHeaderPdf($this->pdf, $this->header1, $this->header2, $this->width);
            $this->page++;
        }
    }

    private function getRecords()
    {
        $records = DeliveryNote
            ::whereIn('id',$this->ids)
            ->orderBy('document_number')
            ->with('designationMaterial.material')
            ->get();

        foreach($records as $item){

            $item->materials = collect();

            if($item->designationMaterial->isNotEmpty()){

                foreach($item->designationMaterial as $material) {

                    $this->all_materials[] = array(
                        'material_id' => $material->material->id,
                        'material' => $material->material->name,
                        'norm' => $material->norm,
                        'unit' => $material->material->unit->unit??'',
                        'code_1c' => $material->material->code_1c,
                        'sort' => 0);

                    $item->materials->push((object)[
                        'material' => $material->material->name,
                        'norm' => $material->norm,
                        'unit' => $material->material->unit->unit??"",
                        'sort' => 0
                    ]);
                }
            }

            $this->node($item->materials,$item->designation_id);

        }

        $this->all_materials = collect($this->all_materials);

        $records->materials = $this->all_materials->groupBy('material_id')->map(function ($group) {
            return [
                'material_id' => $group->first()['material_id'],
                'code_1c' => $group->first()['code_1c'],
                'material' => $group->first()['material'],
                'norm' => $group->sum('norm'),
                'unit' => $group->first()['unit'],
                'sort' => $group->first()['sort'],
            ];
        })->sortBy('material')->sortBy('sort');

        return $records;
    }

    private function node($materials,$designation_id)
    {
        $specifications = Specification
            ::where('designation_id', $designation_id)
            ->with(['designations', 'designationEntry', 'designationMaterial.material.unit'])
            ->get();
        //dd($this->all_materials);
        if ($specifications->isNotEmpty()) {

            foreach ($specifications as $specification) {

                if (str_starts_with($specification->designationEntry->designation, 'КР') || str_starts_with($specification->designationEntry->designation, 'ПИ0')) {
                    $type = str_starts_with($specification->designationEntry->designation, 'КР') ? 'kr' : 'pki';
                    $materials->push((object)[
                        'material' => $specification->designationEntry->designation,
                        'norm' => $specification->quantity,
                        'unit' => $type == 'kr' ? 'шт' : $specification->designationEntry->unit->unit??"",
                        'sort' => $type == 'kr' ? 1 : 2
                    ]);

                    $this->all_materials[] = array(
                        'material_id' => $specification->designationEntry->id.$type,
                        'material' => $specification->designationEntry->designation,
                        'norm' => $specification->quantity,
                        'unit' => $type == 'kr' ? 'шт' : $specification->designationEntry->unit->unit??"",
                        'sort' => $type == 'kr' ? 1 : 2);
                }

                foreach ($specification->designationMaterial as $material) {

                        $this->all_materials[] = array(
                            'material_id' => $material->material->id,
                            'material' => $material->material->name,
                            'norm' => $material->norm,
                            'unit' => $material->material->unit->unit??"",
                            'code_1c' => $material->material->code_1c,
                            'sort' => 0);

                        $materials->push((object)[
                            'material' => $material->material->name,
                            'norm' => $material->norm,
                            'unit' => $material->material->unit->unit??"",
                            'sort' => 0
                        ]);
                }
                $this->node($materials,$specification->designation_entry_id);

            }
        }
        return $materials;
    }

    private function getPdfMaterials($materials)
    {
        //dd($materials);
        // Добавление данных таблицы
        $count = 0;
        foreach ($materials as $item) {

            $count++;
           /* if($count == 10 ){
                dd($item['sort']);
            }*/
            $this->pdf->Ln();

            $this->setNewList();

            $this->pdf->MultiCell($this->width[0], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[1], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[2], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[3], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[4], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[5], $this->height, $item['code_1c'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[6], $this->height, $item['material'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[7], $this->height,$item['unit'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[8], $this->height, $item['sort'] == 0 ? $item['norm'].' * 1.2 = ' : $item['norm'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[9], $this->height, $item['sort'] == 0 ? $item['norm'] * 1.2 : '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

        }

        $this->pdf->Output('delivery_note_'.$this->order_number.'.pdf', 'I');
    }
}
