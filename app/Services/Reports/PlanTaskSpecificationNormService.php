<?php

namespace App\Services\Reports;
use App\Repositories\Interfaces\OrderNameRepositoryInterface;
use App\Repositories\Interfaces\PlanTaskRepositoryInterface;
use App\Services\HelpService\PDFService;
use App\Services\HelpService\SortingService;

class PlanTaskSpecificationNormService
{
    public $width = array(30,120,30,60,60,10);

    public $height = 10;

    public $max_height = 10;
    // Заголовок таблицы
    public $header1 = ['Код 1С',
        'Найменування матеріалів',
        'Од.вимірювання',
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

    public $order_name_id;

    public $first_department = 1;

    public $sender_department_id;

    private PlanTaskRepositoryInterface $planTaskRepository;

    private OrderNameRepositoryInterface $orderNameRepository;

    private SortingService $sortingService;

    public function __construct(PlanTaskRepositoryInterface $planTaskRepository,OrderNameRepositoryInterface $orderNameRepository, SortingService $sortingService)
    {
        $this->planTaskRepository = $planTaskRepository;

        $this->orderNameRepository = $orderNameRepository;

        $this->sortingService = $sortingService;
    }

    public function specificationNorm($order_name_id,$sender_department_id)
    {
        $this->sender_department_id = $sender_department_id;

        $this->order_name_id = $order_name_id;

        $items = $this->planTaskRepository->getByOrderDepartment($this->order_name_id,$this->sender_department_id);

        $groupedData = $this->planTaskRepository->getDataByDepartment($items)->sortBy('name');

        /*----------------------------------------------------------------*/

        $pki_items = $this->planTaskRepository->getByOrderPki($this->order_name_id);

        $pki_groupedData = $this->planTaskRepository->getDataPkiByDepartment($pki_items,$this->sender_department_id);

        /*----------------------------------------------------------------*/

        $kr_items = $this->planTaskRepository->getByOrderKr($this->order_name_id);

        $kr_groupedData = $this->planTaskRepository->getDataKrByDepartment($kr_items, $this->sender_department_id);

        /*----------------------------------------------------------------*/

        $combinedData = $groupedData->merge($pki_groupedData);

        $combinedData = $combinedData->merge($kr_groupedData);

        $combinedData = $combinedData->groupBy('department')->flatMap(function ($items) {
            return $items->sortBy('name')->sortBy('sort');
        });

        $this->getPdf($combinedData);

    }



    private function getPdf($groupedData)
    {
        $order_number = $this->orderNameRepository->getByOrderFirst($this->order_name_id);

        $this->pdf = PDFService::getPdf(array(),array(),$this->width,'СПЕЦИФІКОВАНІ НОРМИ ВИТРАТ МАТЕРІАЛІВ НА ВИРІБ',' ЗАМОВЛЕННЯ №'.$order_number->name);

        $change_department = false;

        // Добавление данных таблицы
        foreach ($groupedData as $item) {

            if( $this->sender_department_id != $item['department'] || !$change_department ){

                $change_department = true;

                $this->sender_department_id = $item['department'];

                $this->setNewList(true);

            }else{

                $this->setNewList(false);

            }
            $this->pdf->MultiCell($this->width[0], $this->height, $item['code_1c'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[1], $this->height, $item['name'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[2], $this->height, $item['unit'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[3], $this->height, $item['norm'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[4], $this->height, $item['norm_with_koef'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[5], $this->height, $item['department'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->Ln();
        }

        // Выводим PDF в браузер
        $this->pdf->Output('specification_norm_'.$order_number->name.'.pdf', 'I');
    }

    private function setNewList($change_department)
    {
        if($change_department){
            if($this->first_department != 1) {
                $this->pdf->AddPage();
                $this->page = 1;
            }

            $this->pdf->Cell(0, 7, 'Цех ' . $this->sender_department_id, 0, 1, 'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
            $this->pdf->Cell(0, 7, 'ЛИСТ ' . $this->page, 0, 1, 'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
            $this->pdf = PDFService::getHeaderPdf($this->pdf, $this->header1, $this->header2, $this->width);
            $this->page++;
            $this->first_department = 0;


        }
        if($this->pdf->getY() >= 179.5) {
            $this->pdf->Cell(0, 7, 'Цех ' . $this->sender_department_id, 0, 1, 'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
            $this->pdf->Cell(0, 7, 'ЛИСТ ' . $this->page, 0, 1, 'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
            $this->pdf = PDFService::getHeaderPdf($this->pdf, $this->header1, $this->header2, $this->width);
            $this->page++;
        }
    }
}
