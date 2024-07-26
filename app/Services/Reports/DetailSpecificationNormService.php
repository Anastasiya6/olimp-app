<?php

namespace App\Services\Reports;
use App\Models\GroupMaterial;
use App\Models\ReportApplicationStatement;
use App\Services\HelpService\PDFService;

class DetailSpecificationNormService
{
    public $width = array(80,40,30,20,50,50);

    public $header1 = ['Найменування матеріалу',
                        'Найменування DSE',
                        'Застосовність',
                        'Од.виміру',
                        'Норма витрат',
                        'Норма на застосування'];

    public $header2 = [ '',
                        '',
                        '',
                        '',
                        'на один',
                        ''];

    public $page = 2;

    public $pdf = null;

    public $height = 10;

    public function detailSpecificationNorm($department,$order_number)
    {
        $data = $this->getData($department,$order_number);

        $this->getPdf($data,$department,$order_number);

    }

    public function getData($department,$order_number)
    {
         $items = ReportApplicationStatement
             ::where('order_number',$order_number)
             /*->whereHas('designation', function ($query) use ($department){
             $query-> whereRaw("SUBSTRING(route, 1, 2) = '$department'");
         })*/
           ->has('designationMaterial.material')
           ->with('designationEntry','designationMaterial.material')
           ->orderBy('order_designationEntry_letters')
           ->orderBy('order_designationEntry')
           ->get();

        $data = $items->flatMap(function ($item) {
            return $item->designationMaterial->map(function ($designationMaterial) use ($item) {
                return [
                    'id' => $designationMaterial->material->id,
                    'material_name' => $designationMaterial->material->name,
                    'detail_name' => $item->designationEntry->designation,
                    'unit' => $designationMaterial->material->unit->unit,
                    'norm' => $designationMaterial->norm,
                    'quantity_total' => $item->quantity_total,
                ];
            });
        });

        $groupedData = $data->groupBy('id')->map(function ($group) {
            // Группируем внутри каждой группы по названию материала
            //return $group->groupBy('material_name')->map(function ($materialDetails, $materialName) {
                // Группируем далее по наименованию детали
            return $group->groupBy('detail_name')->map(function ($details) {
                // Возвращаем детали для каждой детали в группе
                return [
                    'id' => $details->first()['id'],
                    'material_name' => $details->first()['material_name'],
                    'detail_name' => $details->first()['detail_name'],
                    'quantity_total' => $details->sum('quantity_total'),
                    'unit' => $details->first()['unit'],
                    'norm' => $details->first()['norm'],
                ];
            });
        });


        $new_array = array();
        foreach($groupedData as $material_id=>$groupedData_material){
           // dd($groupedData_material);
            $group_materials = GroupMaterial::where('material_id',$material_id)->get();
            if($group_materials->count() > 0) {

                $group_materials->load('materialEntry');

                foreach ($group_materials as $group) {

                    foreach($groupedData_material as $detail){
                        $detail['norm'] = $detail['norm']*$group->norm;
                        $detail['unit'] = $group->materialEntry->unit->unit;
                        $new_array[$group->materialEntry->name.$group->material_entry_id."_Group"][$group->materialEntry->name][] = $detail;
                    }
                    usort($new_array[$group->materialEntry->name . $group->material_entry_id . "_Group"][$group->materialEntry->name], function ($a, $b) {
                        return strcmp($a['detail_name'], $b['detail_name']);
                    });
                }

            }else{
                foreach($groupedData_material as $detail){
                    $new_array[$detail['material_name'].$material_id][$detail['material_name']][] = $detail;
                }
                usort($new_array[$detail['material_name'].$material_id][$detail['material_name']], function ($a, $b) {
                    return strcmp($a['detail_name'], $b['detail_name']);
                });
            }
        }
        ksort($new_array);

        return $new_array;
    }

    public function getPdf($data,$department,$order_number)
    {
        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'ПОДЕТАЛЬНО-СПЕЦИФІКОВАННІ НОРМИ ВИТРАТ МАТЕРІАЛІВ НА ВИРІБ','ЦЕХ '.$department.' ЗАМОВЛЕННЯ '.$order_number);

        $sum_norm = 0;

        foreach ($data as $row) {

            $this->newList();
            foreach($row as $material_name=>$detail_){

                $this->pdf->Cell(100, $this->height, $material_name);
                $this->pdf->Ln();

                foreach($detail_ as $detail) {
                    //if($detail['norm'] * $detail['quantity_total'] = '0.00004')
                       // dd(number_format($detail['norm'] * $detail['quantity_total'],6));
                    $this->newList();
                    $this->pdf->Cell($this->width[0], $this->height, '');
                    $this->pdf->Cell($this->width[1], $this->height, $detail['detail_name']);
                    $this->pdf->Cell($this->width[2], $this->height, $detail['quantity_total']);
                    $this->pdf->Cell($this->width[3], $this->height, $detail['unit']);
                    $this->pdf->Cell($this->width[4], $this->height, number_format($detail['norm'],6));
                    $this->pdf->Cell($this->width[5], $this->height, number_format($detail['norm'] * $detail['quantity_total'],6));
                    $this->pdf->Ln();
                    $sum_norm = $sum_norm + $detail['norm'] * $detail['quantity_total'];
                }
            }
            $this->newList();
            $this->pdf->Cell(270, 10, 'Разом по матер. '.$sum_norm,0,1,'R');
            $this->pdf->Ln();
            $sum_norm = 0;

        }

        // Выводим PDF в браузер
        $this->pdf->Output('example.pdf', 'I');
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
