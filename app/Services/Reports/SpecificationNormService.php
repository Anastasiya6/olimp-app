<?php

namespace App\Services\Reports;
use App\Repositories\Interfaces\OrderNameRepositoryInterface;
use App\Repositories\Interfaces\ReportApplicationStatementRepositoryInterface;
use App\Services\HelpService\PDFService;
use App\Services\HelpService\SortingService;

class SpecificationNormService
{
    public $width = array(30,120,20,50,50,10);

    public $height = 10;

    public $max_height = 10;
    // Заголовок таблицы
    public $header1 = ['Код 1С',
                        'Найменування матеріалів',
                        'Од.',
                        'Норма витрат на виріб',
                        'Разом * коеф.',
                        'Цех'];
    public $header2 = ['',
                        '',
                        'виміру',
                        '',
                        '',
                        ''];
    public $pdf = null;

    public $page = 1;

    public $order_name_id;

    public $first_department = 1;

    public $department = 'no department';

    private ReportApplicationStatementRepositoryInterface $reportApplicationStatementRepository;

    private OrderNameRepositoryInterface $orderNameRepository;

    private SortingService $sortingService;

    public function __construct(ReportApplicationStatementRepositoryInterface $reportApplicationStatementRepository,OrderNameRepositoryInterface $orderNameRepository,SortingService $sortingService)
    {
        $this->reportApplicationStatementRepository = $reportApplicationStatementRepository;

        $this->orderNameRepository = $orderNameRepository;

        $this->sortingService = $sortingService;
    }

    public function specificationNorm($order_name_id,$department)
    {
        /*$this->department = 0 - Всі цеха*/
        $this->department = $department;

        $this->order_name_id = $order_name_id;

        $items = $this->reportApplicationStatementRepository->getByOrder($this->order_name_id);

        $groupedData = $this->reportApplicationStatementRepository->getDataByDepartment($items,$this->department);

        /*----------------------------------------------------------------*/

        $pki_items = $this->reportApplicationStatementRepository->getByOrderPki($this->order_name_id);

        $pki_groupedData = $this->reportApplicationStatementRepository->getDataPkiByDepartment($pki_items,$this->department);

        /*----------------------------------------------------------------*/

        $kr_items = $this->reportApplicationStatementRepository->getByOrderKr($this->order_name_id);

        $kr_groupedData = $this->reportApplicationStatementRepository->getDataKrByDepartment($kr_items,$this->department);

        /*----------------------------------------------------------------*/

        $combinedData = $groupedData->merge($pki_groupedData);

        $combinedData = $combinedData->merge($kr_groupedData);

        if($this->department == 0){
            $combinedData = $this->sortingService->getSortbyDepartment($combinedData);
        }

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
            //dd($this->department, $item['department']);

            if( $this->department != $item['department'] || !$change_department ){

                $change_department = true;

                $this->department = $item['department'];

                $this->setNewList(true);

            }else{

               $this->setNewList(false);

            }
            $this->pdf->MultiCell($this->width[0], $this->height, $item['code_1c']??'', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
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
