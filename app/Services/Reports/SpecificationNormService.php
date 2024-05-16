<?php

namespace App\Services\Reports;
use App\Models\Material;
use App\Models\ReportApplicationStatement;
use App\Services\HelpService\PDFService;
use TCPDF;

class SpecificationNormService
{
    public $width = array(120,30,60,60,10);

    public $height = 10;

    public $max_height = 10;
    // Заголовок таблицы
    public $header1 = ['Найменування матеріалів',
                        'Од.виміру',
                        'Норма витрат на виріб',
                        'Разом * 1.2',
                        'Цех'];
    public $header2 = ['',
                        '',
                        '',
                        '',
                        ''];
    public $pdf = null;

    public $page = 2;

    public function specificationNorm($order_number)
    {
        $items = ReportApplicationStatement
            ::where('order_number',$order_number)
            ->has('designationMaterial.material')
            ->with('designationEntry','designationMaterial.material')
            ->get();

        $data = $items->flatMap(function ($item) {
            return $item->designationMaterial->map(function ($designationMaterial) use ($item) {
                return [
                    'id' => $designationMaterial->material->id,
                    'name' => $designationMaterial->material->name,
                    'unit' => $designationMaterial->material->unit->unit,
                    'norm' => $designationMaterial->norm * $item->quantity_total,
                    'department' => substr($item->designationEntry->route, 0, 2),
                ];
            });
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
                'name' => $item->designationEntry->name,
                'unit' => $item->designationEntry->unit->unit??'шт',
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

        $kr_items = ReportApplicationStatement
            ::whereHas('designationEntry', function ($query) {
                $query->where('designation', 'like', 'КР%');
            })
            ->where('order_number',$order_number)
            ->with('designationEntry')
            ->get();


        $kr_data = $kr_items->map(function ($item) {
            return [
                'id' => $item->designationEntry->id.'kr',
                'name' => $item->designationEntry->name,
                'unit' => 'шт',
                'norm' =>$item->quantity_total,
                'department' => substr($item->designationEntry->route, 0, 2),
            ];
        });

        $kr_groupedData = $kr_data->groupBy('id')->map(function ($items) {
            return [
                'name' => $items->first()['name'], // Берем название материала из первого элемента группы
                'unit' => $items->first()['unit'], // Берем единицу измерения из первого элемента группы
                'department' => $items->first()['department'], // Берем цех из первого элемента группы
                'norm' => $items->sum('norm'), // Суммируем количество по всем элементам группы
                'norm_with_koef' => $items->sum('norm')
            ];
        });
        $kr_groupedData = $kr_groupedData->sortBy('name');

        $combinedData = $groupedData->merge($pki_groupedData);

        $combinedData = $combinedData->merge($kr_groupedData);
       // dd($combinedData);
        $this->getPdf($combinedData,$order_number);

    }
    public function getPdf($groupedData,$order_number)
    {

        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'СПЕЦИФІКОВАНІ НОРМИ ВИТРАТ МАТЕРІАЛІВ НА ВИРІБ',' ЗАКАЗ №'.$order_number);

        // Добавление данных таблицы
        foreach ($groupedData as $item) {
            if($this->pdf->getY() >= 185) {
                $this->pdf->Cell(0, 5, 'ЛИСТ '.$this->page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                $this->pdf = PDFService::getHeaderPdf($this->pdf, $this->header1, $this->header2, $this->width);
                $this->page++;
            }
            $this->pdf->MultiCell($this->width[0], $this->height, $item['name'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[1], $this->height, $item['unit'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[2], $this->height, $item['norm'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[3], $this->height, $item['norm_with_koef'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[4], $this->height, $item['department'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->Ln();
        }

        // Выводим PDF в браузер
        $this->pdf->Output('specification_norm_'.$order_number.'.pdf', 'I');
    }
}
