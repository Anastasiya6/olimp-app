<?php

namespace App\Services\Reports;
use App\Repositories\Interfaces\DetailSpecificationRepositoryInterface;
use App\Repositories\Interfaces\OrderNameRepositoryInterface;
use App\Services\HelpService\PDFService;

class DetailSpecificationNormService
{
    public $width = array(80,40,30,20,50,50);

    public $header1 = ['Найменування матеріалу',
                        'Найменування DSE',
                        'Застосовність',
                        'Од.вимір.',
                        'Норма витрат',
                        'Норма на застосування'];

    public $header2 = [ '',
                        '',
                        'на DSE',
                        '',
                        'на один',
                        ''];

    public $page = 2;

    public $pdf = null;

    public $height = 10;

    private $orderNameRepository;

    private $detailSpecificationRepository;

    public function __construct(OrderNameRepositoryInterface $orderNameRepository, detailSpecificationRepositoryInterface $detailSpecificationRepository)
    {

        $this->orderNameRepository = $orderNameRepository;

        $this->detailSpecificationRepository = $detailSpecificationRepository;
    }

    public function detailSpecificationNorm($order_name_id,$department)
    {
        $items = $this->getData($order_name_id,$department);

        $pki_kr_items = $this->detailSpecificationRepository->getByOrderDepartmentPkiKrItems($order_name_id,$department);

        $itemsCollection = collect($items);

        $combinedData = $itemsCollection->merge($pki_kr_items);

        $this->getPdf($combinedData,$department,$order_name_id);

    }

    public function getData($order_name_id,$department): array
    {
        $items = $this->detailSpecificationRepository->getByOrderDepartment($order_name_id,$department);

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
        $groupedData = $this->detailSpecificationRepository->groupData($data);

        return $this->detailSpecificationRepository->addGroupMaterial($groupedData);
    }

    public function getPdf($data,$department,$order_name_id)
    {
        $order_number = $this->orderNameRepository->getByOrderFirst($order_name_id);

        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'ПОДЕТАЛЬНО-СПЕЦИФІКОВАННІ НОРМИ ВИТРАТ МАТЕРІАЛІВ НА ВИРІБ','ЦЕХ '.$department.' ЗАМОВЛЕННЯ '.$order_number->name);

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
