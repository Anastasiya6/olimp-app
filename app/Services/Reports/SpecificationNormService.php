<?php

namespace App\Services\Reports;
use App\Models\ReportApplicationStatement;
use App\Services\HelpService\PDFService;

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

    public $page = 1;

    public $order_number;

    public $first_department = 1;

    public $department = 'no department';

    public function specificationNorm($order_number,$department)
    {
        /*$this->department = 0 - Всі цеха*/
        $this->department = $department;

        $this->order_number = $order_number;

        $items = $this->getItems();

        $groupedData = $this->getDataByDepartment($items);

        /*----------------------------------------------------------------*/

        $pki_items = $this->getPkiItems();

        $pki_groupedData = $this->getDataPkiByDepartment($pki_items);

        /*----------------------------------------------------------------*/

        $kr_items = $this->getKrItems();

        $kr_groupedData = $this->getDataKrByDepartment($kr_items);

        /*----------------------------------------------------------------*/

        $combinedData = $groupedData->merge($pki_groupedData);

        $combinedData = $combinedData->merge($kr_groupedData);

        $combinedData = $this->getSortbyDepartment($combinedData);

        $combinedData = $combinedData->groupBy('department')->flatMap(function ($items) {
            return $items->sortBy('name')->sortBy('sort');
        });

        $this->getPdf($combinedData);

    }
    private function getItems()
    {
        return ReportApplicationStatement
            ::where('order_number',$this->order_number)
            ->has('designationMaterial.material')
            ->with('designationEntry','designationMaterial.material')
            ->get();
    }

    private function getPkiItems()
    {
        return ReportApplicationStatement
            ::whereHas('designationEntry', function ($query) {
                $query->where('type', 1);
            })
            ->where('order_number',$this->order_number)
            ->with('designationEntry')
            ->get();
    }

    private function getKrItems()
    {
        return ReportApplicationStatement
            ::whereHas('designationEntry', function ($query) {
                $query->where('designation', 'like', 'КР%');
            })
            ->where('order_number',$this->order_number)
            ->with('designationEntry')
            ->get();
    }

    private function getSortByDepartment($data)
    {
        if($this->department != 0 )
            return $data;
        return $data->sort(function ($a, $b) {
            // Если у первого элемента нет цеха, а у второго есть - второй элемент должен быть выше
            if (empty($a['department']) && !empty($b['department'])) {
                return 1;
            }

            // Если у второго элемента нет цеха, а у первого есть - первый элемент должен быть выше
            if (!empty($a['department']) && empty($b['department'])) {
                return -1;
            }

            // Если у обоих элементов есть цеха или у обоих нет - сортируем по названию цеха
            return strcmp($a['department'], $b['department']);
        });
    }

    private function getDataPkiByDepartment($pki_items)
    {
        $pki_data = $pki_items->map(function ($item) {
            return [
                'id' => $item->designationEntry->id.'pki',
                'name' => $item->designationEntry->name,
                'unit' => $item->designationEntry->unit->unit??'шт',
                'norm' =>$item->quantity_total,
                'department' => substr($item->tm, -2),
            ];
        });
        if ($this->department != 0){
            $pki_data = $pki_data->filter(function ($item) {
                return $item['department'] == $this->department;
            });
        }

        return $pki_data->groupBy('id')->flatMap(function ($items) {
            return $items->groupBy('department')->map(function ($departmentItems) {
                return [
                    'id' => $departmentItems->first()['id'], // Берем ID из первого элемента группы
                    'name' => $departmentItems->first()['name'], // Берем название материала из первого элемента группы
                    'unit' => $departmentItems->first()['unit'], // Берем единицу измерения из первого элемента группы
                    'department' => $departmentItems->first()['department'], // Берем цех из первого элемента группы
                    'norm' => $departmentItems->sum('norm'), // Суммируем количество по всем элементам группы
                    'norm_with_koef' => $departmentItems->sum('norm'),
                    'sort' => 2
                ];
            })->values();
        });
    }

    private function getDataKrByDepartment($kr_items)
    {
        $kr_data = $kr_items->map(function ($item) {
            return [
                'id' => $item->designationEntry->id.'kr',
                'name' => $item->designationEntry->name,
                'unit' => 'шт',
                'norm' =>$item->quantity_total,
                'department' => substr($item->tm, -2),
            ];
        });

        if ($this->department != 0){
            $kr_data = $kr_data->filter(function ($item) {
                return $item['department'] == $this->department;
            });
        }

        return $kr_data->groupBy('id')->flatMap(function ($items) {
            return $items->groupBy('department')->map(function ($departmentItems) {

                return [
                'name' => $departmentItems->first()['name'], // Берем название материала из первого элемента группы
                'unit' => $departmentItems->first()['unit'], // Берем единицу измерения из первого элемента группы
                'department' => $departmentItems->first()['department'], // Берем цех из первого элемента группы
                'norm' => $departmentItems->sum('norm'), // Суммируем количество по всем элементам группы
                'norm_with_koef' => $departmentItems->sum('norm'),
                'sort' => 1
                ];
            })->values();
        });
    }

    private function getDataByDepartment($items)
    {
        if ($this->department != 0){
            $data = $items->flatMap(function ($item) {
                return $item->designationMaterial->filter(function ($designationMaterial) use ($item) {
                    // Проверка на соответствие department
                    $department = substr($item->designationEntry->route, 0, 2);
                    return $department == $this->department;
                })->map(function ($designationMaterial) use ($item) {
                    return [
                        'id' => $designationMaterial->material->id,
                        'name' => $designationMaterial->material->name,
                        'unit' => $designationMaterial->material->unit->unit,
                        'norm' => $designationMaterial->norm * $item->quantity_total,
                        'department' => substr($item->designationEntry->route, 0, 2),
                    ];
                });
            })->sortBy('id');

        }else {

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
            })->sortBy('id');
        }
        return $data->groupBy('id')->flatMap(function ($items) {
            return $items->groupBy('department')->map(function ($departmentItems) {
                return [
                    'id' => $departmentItems->first()['id'], // Берем ID из первого элемента группы
                    'name' => $departmentItems->first()['name'], // Берем название материала из первого элемента группы
                    'unit' => $departmentItems->first()['unit'], // Берем единицу измерения из первого элемента группы
                    'department' => $departmentItems->first()['department'], // Берем цех из первого элемента группы
                    'norm' => $departmentItems->sum('norm'), // Суммируем количество по всем элементам группы
                    'norm_with_koef' => $departmentItems->sum('norm') * 1.2,
                    'sort' => 0
                ];
            })->values(); // Преобразуем коллекцию в массив значений
        });
    }

    private function getPdf($groupedData)
    {
        $this->pdf = PDFService::getPdf(array(),array(),$this->width,'СПЕЦИФІКОВАНІ НОРМИ ВИТРАТ МАТЕРІАЛІВ НА ВИРІБ',' ЗАКАЗ №'.$this->order_number);

        $change_department = false;

        // Добавление данных таблицы
        foreach ($groupedData as $item) {
            //dd($this->department, $item['department']);

            if( $this->department != $item['department'] || !$change_department ){

                $change_department = true;

                $this->department = $item['department'];

                $this->newList(true);

            }else{

               $this->newList(false);

            }

            $this->pdf->MultiCell($this->width[0], $this->height, $item['name'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[1], $this->height, $item['unit'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[2], $this->height, $item['norm'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[3], $this->height, $item['norm_with_koef'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[4], $this->height, $item['department'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->Ln();
        }

        // Выводим PDF в браузер
        $this->pdf->Output('specification_norm_'.$this->order_number.'.pdf', 'I');
    }

    private function newList($change_department)
    {
        if($change_department){
            if($this->first_department != 1) {
                $this->pdf->AddPage();
                $this->page = 1;
            }

                $this->pdf->Cell(0, 7, 'Цех ' . $this->department, 0, 1, 'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                $this->pdf->Cell(0, 7, 'ЛИСТ ' . $this->page, 0, 1, 'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                $this->pdf = PDFService::getHeaderPdf($this->pdf, $this->header1, $this->header2, $this->width);
            $this->page++;
            $this->first_department = 0;


        }
        if($this->pdf->getY() >= 179.5) {
            $this->pdf->Cell(0, 7, 'Цех ' . $this->department, 0, 1, 'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
            $this->pdf->Cell(0, 7, 'ЛИСТ ' . $this->page, 0, 1, 'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                $this->pdf = PDFService::getHeaderPdf($this->pdf, $this->header1, $this->header2, $this->width);
                $this->page++;
        }
    }
}
