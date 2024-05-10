<?php

namespace App\Services\Reports;
use App\Models\Material;
use App\Models\ReportApplicationStatement;
use App\Services\HelpService\PDFService;
use TCPDF;

class SpecificationNormService
{
    public function specificationNorm($order_number)
    {
        /*$items = Material::query()
        ->join('designation_materials', 'materials.id', '=', 'designation_materials.material_id')
        ->join('report_application_statements', 'report_application_statements.designation_entry_id', '=', 'designation_materials.designation_id')
        ->join('designations', 'designations.id', '=', 'report_application_statements.designation_entry_id')
        ->where('report_application_statements.order_number', $order_number)
        ->select('materials.*', 'designations.designation as designation_name','report_application_statements.quantity_total','designation_materials.norm')
        ->with('designationMaterial')
        ->orderBy('materials.name')
        ->orderBy('order_designationEntry_letters')
        ->orderBy('order_designationEntry')
        ->get();
        foreach($items as $item){
            dd($item);
        }*/

        $items = ReportApplicationStatement
            ::where('order_number',$order_number)
            ->has('designationMaterial.material')
            ->with('designationEntry','designationMaterial')
            ->get();

        $data = $items->map(function ($item) {
            return [
                'id' => $item->designationMaterial->material->id,
                'name' => $item->designationMaterial->material->name,
                'unit' => $item->designationMaterial->material->unit->unit,
                'norm' => $item->designationMaterial->norm * $item->quantity_total,
                'department' => substr($item->designationEntry->route, 0, 2),
            ];
        });

        $groupedData = $data->groupBy('id')->map(function ($items) {
            return [
                'name' => $items->first()['name'], // Берем название материала из первого элемента группы
                'unit' => $items->first()['unit'], // Берем единицу измерения из первого элемента группы
                'department' => $items->first()['department'], // Берем цех из первого элемента группы
                'norm' => $items->sum('norm'), // Суммируем количество по всем элементам группы
                'norm_with_koef' => $items->sum('norm') * 1.2
            ];
        });
        $groupedData = $groupedData->sortBy('name');

        $pki_items = ReportApplicationStatement
            ::whereHas('designationEntry', function ($query) {
                $query->where('type', 1);
            })
            ->where('order_number',$order_number)
            ->with('designationEntry')
            ->get();


        $pki_data = $pki_items->map(function ($item) {
            return [
                'id' => $item->designationEntry->id.'pki',
                'name' => $item->designationEntry->designation,
                'unit' => $item->designationEntry->unit->unit,
                'norm' =>$item->quantity_total,
                'department' => substr($item->designationEntry->route, 0, 2),
            ];
        });

        $pki_groupedData = $pki_data->groupBy('id')->map(function ($items) {
            return [
                'name' => $items->first()['name'], // Берем название материала из первого элемента группы
                'unit' => $items->first()['unit'], // Берем единицу измерения из первого элемента группы
                'department' => $items->first()['department'], // Берем цех из первого элемента группы
                'norm' => $items->sum('norm'), // Суммируем количество по всем элементам группы
                'norm_with_koef' => $items->sum('norm')
            ];
        });
        $pki_groupedData = $pki_groupedData->sortBy('name');

        $combinedData = $groupedData->merge($pki_groupedData);

        $this->getPdf($combinedData,$order_number);

    }
    public function getPdf($groupedData,$order_number)
    {
        $width = array(120,30,70,70,10);

        // Заголовок таблицы
        $header1 = ['Найменування матеріалів',
            'Од.виміру',
            'Норма витрат на виріб',
            'Разом * 1.2',
            'Цех'];
        $header2 = ['',
            '',
            '',
            '',
            ''];
        $pdf = PDFService::getPdf($header1,$header2,$width,'СПЕЦИФІКОВАНІ НОРМИ ВИТРАТ МАТЕРІАЛІВ НА ВИРІБ',' ЗАКАЗ №'.$order_number);
        $page = 2;
        // Добавление данных таблицы
        foreach ($groupedData as $item) {
            if($pdf->getY() >= 185) {
                $pdf->Cell(0, 5, 'ЛИСТ '.$page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                $pdf = PDFService::getHeaderPdf($pdf, $header1, $header2, $width);
                $page++;
            }
            $pdf->Cell($width[0], 10, $item['name']);
            $pdf->Cell($width[1], 10, $item['unit']);
            $pdf->Cell($width[2], 10, $item['norm']);
            $pdf->Cell($width[3], 10, $item['norm_with_koef']);
            $pdf->Cell($width[4], 10, $item['department']);
            $pdf->Ln();
        }

        // Выводим PDF в браузер
        $pdf->Output('specification_norm_'.$order_number.'.pdf', 'I');
    }
}
