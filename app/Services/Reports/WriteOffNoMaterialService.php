<?php

namespace App\Services\Reports;
use App\Models\DeliveryNote;
use App\Models\OrderName;
use App\Models\Specification;
use App\Repositories\Interfaces\OrderNameRepositoryInterface;
use App\Services\HelpService\HelpService;
use App\Services\HelpService\NoMaterialService;
use App\Services\HelpService\PDFService;

class WriteOffNoMaterialService
{
    public $width = array(25,25,50,150,30);

    public $height = 10;

    public $max_height = 10;
    // Заголовок таблицы
    public $header1 = ['Номер',
                        'Дата',
                        'Номер',
                        'Назва деталі',
                        'К-ть'];
    public $header2 = ['документа',
                        'документа',
                        'деталі',
                        '',
                        ''
];
    public $pdf = null;

    public $page = 2;

    public $start_date;

    public $end_date;

    public $sender_department_id;

    public $receiver_department_id;

    public $order_name_id;

    public OrderName $order;

    public $selectedItems;

    private OrderNameRepositoryInterface $orderNameRepository;

    public function __construct( OrderNameRepositoryInterface $orderNameRepository)
    {
        $this->orderNameRepository = $orderNameRepository;
    }

    public function writeOffNoMaterial($order_name_id,$start_date,$end_date,$sender_department,$receiver_department)
    {
        $this->order_name_id = $order_name_id;

        $this->start_date = $start_date;

        $this->end_date = $end_date;

        $this->sender_department_id = $sender_department;

        $this->receiver_department_id = $receiver_department;

        $this->getRecords();

        $start_date_str = \Carbon\Carbon::parse($this->start_date)->format('d.m.Y');

        $end_date_str = \Carbon\Carbon::parse($this->end_date)->format('d.m.Y');

        $this->order = $this->orderNameRepository->getByOrderFirst($this->order_name_id);

        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'ВІДСУТНІ МАТЕРІАЛИ НА ЗДАТОЧНІ З '.$start_date_str.' ПО '.$end_date_str,' ЗАМОВЛЕННЯ №'.$this->order->name);

        $this->getDetailPdf($this->selectedItems);

    }

    private function getDetailPdf($records)
    {
        foreach ($records as $item) {

            $this->setNewList();

            $this->pdf->MultiCell($this->width[0], $this->height, $item->document_number, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[1], $this->height, \Carbon\Carbon::parse($item->document_date)->format('d.m.Y'), 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[2], $this->height, $item->designation->designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[3], $this->height, $item->designation->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[4], $this->height, $item->quantity, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->Ln();
        }
        // Выводим PDF в браузер
        $this->pdf->Output('delivery_note_details_'.$this->order->name.'.pdf', 'I');
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
        $items = DeliveryNote::withFilters( $this->start_date,
            $this->end_date,
            $this->order_name_id,
            $this->sender_department_id,
            $this->receiver_department_id)
            ->with('designationMaterial.material')
            ->orderBy('document_number')
            ->get();

       foreach($items as $item){

            $item->material = 1;

            if($item->designationMaterial->isEmpty()){
                $item->material = 0;
            }
            $item->material = NoMaterialService::noMaterial($item->designation_id,$item->designationMaterial->isNotEmpty());
            if($item->material == 0){
                $this->selectedItems[] = $item;
            }
        }
    }

}
