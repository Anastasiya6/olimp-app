<?php

namespace App\Services\Reports;
use App\Models\DesignationMaterial;
use App\Models\GroupMaterial;
use App\Models\Material;
use App\Models\ReportApplicationStatement;
use App\Services\HelpService\PDFService;
use App\Services\Statements\ApplicationStatementPrintService;
use TCPDF;

class DetailSpecificationNormService
{
    public function detailSpecificationNorm($department,$order_number)
    {
        $data = $this->getData($department,$order_number);

        $this->getPdf($data,$department,$order_number);

    }

    public function getData($department,$order_number)
    {
        /* $items = ReportApplicationStatement
             ::where('order_number',$order_number)
             /*->whereHas('designation', function ($query) use ($department){
             $query-> whereRaw("SUBSTRING(route, 1, 2) = '$department'");
         })*/
        /*   ->has('designationMaterial.material')
           ->with('designationEntry','designationMaterial.material')
           ->orderBy('order_designationEntry_letters')
           ->orderBy('order_designationEntry')
           ->get();*/

        $items = Material::query()
            // ->leftJOIN('group_materials', 'materials.id', '=','group_materials.material_id')
            ->join('designation_materials', 'materials.id', '=', 'designation_materials.material_id')
            ->join('report_application_statements', 'report_application_statements.designation_entry_id', '=', 'designation_materials.designation_id')
            ->join('designations', 'designations.id', '=', 'report_application_statements.designation_entry_id')
            ->where('report_application_statements.order_number', $order_number)
            ->select('materials.*', 'designations.designation as designation_name', 'report_application_statements.quantity_total', 'designation_materials.norm', 'report_application_statements.designation_entry_id')
            ->orderBy('materials.name')
            ->orderBy('order_designationEntry_letters')
            ->orderBy('order_designationEntry')
            ->with('groupMaterials')
            ->get();
;
        $data = $items->sortBy('id')->map(function ($item) {
            return [
                'id' => $item->id,
                'material_name' => $item->name,
                'detail_name' => $item->designation_name,
                'quantity_total' => $item->quantity_total,
                'unit' => $item->unit->unit,
                'norm' => $item->norm,
                'group_materials' => $item->groupMaterials
            ];
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
                    'group_materials' => $details->first()['group_materials']
                ];
            });
        });


        $new_array = array();
        foreach($groupedData as $material_id=>$groupedData_material){

            $group_materials = GroupMaterial::where('material_id',$material_id)->get();
            if($group_materials->count() > 0) {
                $group_materials->load('materialEntry');

                foreach ($group_materials as $group) {

                    foreach($groupedData_material as $detail){
                        $detail['norm'] = $detail['norm']*$group->norm;
                        $new_array[$group->material_entry_id."_Group"][$group->materialEntry->name][] = $detail;
                    }
                }
            }else{
                foreach($groupedData_material as $detail){
                    $new_array[$material_id][$detail['material_name']][] = $detail;

                }
            }
        }

        return $new_array;
    }

    public function getPdf($data,$department,$order_number)
    {

        $width = array(80,40,30,20,50,50);
        $header1 = ['Найменування матеріалу',
            'Найменування DSE',
            'Застосовність',
            'Од.виміру',
            'Норма витрат',
            'Норма на застосування'];

        $header2 = [ '',
            '',
            '',
            '',
            'на один',
            ''];

        $pdf = PDFService::getPdf($header1,$header2,$width,'ПОДЕТАЛЬНО-СПЕЦИФІКОВАННІ НОРМИ ВИТРАТ МАТЕРІАЛІВ НА ВИРІБ','ЦЕХ '.$department.' ЗАКАЗ '.$order_number);
        $page = 2;

        // Устанавливаем стиль линии
        $pdf->SetLineStyle(array('dash' => 2, 'color' => array(0, 0, 0))); // Пунктирная линия черного цвета

        // Устанавливаем шрифт и размер текста
        $pdf->SetFont('dejavusans', '', 10);
        $sum_norm = 0;

        foreach ($data as $material_id=>$row) {

            if($pdf->getY() >= 185) {
                $pdf->Cell(0, 5, 'ЛИСТ '.$page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                $pdf = PDFService::getHeaderPdf($pdf, $header1, $header2, $width);
                $page++;
            }
            foreach($row as $material_name=>$detail_){

                $pdf->Cell(100, 10, $material_name);
                $pdf->Ln();
                foreach($detail_ as $detail) {

                    $pdf->Cell($width[0], 10, '');
                    $pdf->Cell($width[1], 10, $detail['detail_name']);
                    $pdf->Cell($width[2], 10, $detail['quantity_total']);
                    $pdf->Cell($width[3], 10, $detail['unit']);
                    $pdf->Cell($width[4], 10, $detail['norm']);
                    $pdf->Cell($width[5], 10, $detail['norm'] * $detail['quantity_total']);
                    $pdf->Ln();
                    $sum_norm = $sum_norm + $detail['norm'] * $detail['quantity_total'];
                }
            }

            $pdf->Cell(270, 10, 'Разом по матер. '.$sum_norm,0,1,'R');
            $pdf->Ln();
            $sum_norm = 0;

        }

        // Выводим PDF в браузер
        $pdf->Output('example.pdf', 'I');
    }
}
