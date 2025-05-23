<?php

namespace App\Services\Reports;
use App\Models\DeliveryNote;
use App\Models\OrderName;
use App\Repositories\Interfaces\OrderNameRepositoryInterface;
use App\Services\HelpService\MaterialService;
use App\Services\HelpService\PDFService;

class WriteOffService
{
    public $width = array(17,23,35,40,10,15,30,50,8,35,23);

    public $width1 = array(16,50,100,30);

    public $height = 10;

    public $max_height = 10;
    // Заголовок таблицы
    public $header1 = ['Номер',
                        'Дата',
                        'Номер',
                        'Назва',
                        'К-ть',
                        'Замовл.',
                        'Код 1C',
                        'Матеріал',
                        'Од',
                        'Норма',
                        'Норма*коеф.'];
    public $header2 = ['докумен.',
                        'докумен.',
                        'деталі',
                        'деталі',
                        '',
                        '',
                        '',
                        '',
                        '.вим.',
                        '',
                        ''];
    public $header3 = [
        'Номер',
        'Номер деталі',
        'Назва деталі',
        'Кількість'
  ];
    public $header4 = [
        'докумен.',
        '',
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

    public $ids;

    public $materialService;

    public $type_report = 0;

    private OrderNameRepositoryInterface $orderNameRepository;

    public function __construct(OrderNameRepositoryInterface $orderNameRepository, MaterialService $service )
    {
        $this->orderNameRepository = $orderNameRepository;

        $this->materialService = $service;
    }

    public function writeOff($ids,$order_name_id,$start_date,$end_date,$sender_department_id,$receiver_department_id,$type_report = 0)
    {
        $this->ids = json_decode($ids);

        $this->type_report = $type_report;

        $this->order_name_id = $order_name_id;

        $this->start_date = $start_date;

        $this->end_date = $end_date;

        $this->sender_department_id = $sender_department_id;

        $this->receiver_department_id = $receiver_department_id;

        $records = $this->getRecords();

        $start_date_str = \Carbon\Carbon::parse($this->start_date)->format('d.m.Y');

        $end_date_str = \Carbon\Carbon::parse($this->end_date)->format('d.m.Y');

        $this->order = $this->orderNameRepository->getByOrderFirst($this->order_name_id);

        /*Report by delivery notes*/
        if($this->type_report == 0) {

            $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'ЗДАТОЧНІ З '.$start_date_str.' ПО '.$end_date_str,' ЗАМОВЛЕННЯ №'.$this->order->name);

            $this->getDeliveryNotePdf($records);

        /*Report together by materials*/
        }elseif($this->type_report == 1){

            $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'ЗДАТОЧНІ З '.$start_date_str.' ПО '.$end_date_str,' ЗАМОВЛЕННЯ №'.$this->order->name);

            $this->getMaterialPdf($records);

            /*Report by details*/
        }elseif($this->type_report == 2){

            $this->pdf = PDFService::getPdf($this->header3,$this->header4,$this->width1,'ЗДАТОЧНІ З '.$start_date_str.' ПО '.$end_date_str,' ЗАМОВЛЕННЯ №'.$this->order->name,'P');

            $this->getDetailPdf($records);

        }
    }

    private function getDetailPdf($records)
    {
        foreach ($records as $item) {

            $this->setNewList($this->header3,$this->header4,$this->width1,270);

            $this->pdf->MultiCell($this->width1[0], $this->height, $item->document_number, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width1[1], $this->height, $item->designation->designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width1[2], $this->height, $item->designation->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width1[3], $this->height, $item->quantity, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->Ln();
        }

        // Выводим PDF в браузер
        $this->pdf->Output('write_off_details_'.$this->order->name.'.pdf', 'I');
    }

    private function getDeliveryNotePdf($records)
    {
        //dd($records);
        foreach ($records as $item_record) {

            $this->setNewList($this->header1,$this->header2,$this->width);

            $this->pdf->MultiCell($this->width[0], $this->height, $item_record->document_number, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[1], $this->height, \Carbon\Carbon::parse($item_record->document_date)->format('d.m.Y'), 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[2], $this->height, $item_record->designation->designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[3], $this->height, $item_record->designation->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[4], $this->height, $item_record->quantity, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[5], $this->height, $item_record->orderName->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[6], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $first = 0;
           // dd($item_record);
            if($item_record->materials) {

                $materials = $item_record->materials->sortBy('material')->sortBy('sort');

                foreach ($materials as $item) {
                    $column = 6;
                    $first++;

                    $this->setNewList($this->header1,$this->header2,$this->width);

                    if ($first > 1) {

                        $this->pdf->Ln();

                        for($i=0;$i<=$column;$i++) {
                           $this->pdf->MultiCell($this->width[$i], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
                        }
                    }

                    $this->pdf->MultiCell($this->width[++$column], $this->height, $item['material'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                    $this->pdf->MultiCell($this->width[++$column], $this->height, $item['unit'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                    $this->pdf->MultiCell($this->width[++$column], $this->height, $item['sort'] == 0 ? $item['print_number'] . $item['multiplier_str'] . ' = ' :  $item['print_number'] /*$norm->sort == 0 ? $norm->norm .' * '.$norm->quantity.' * '. $item->quantity.' * '. $norm->pred_quantity_node. $multiplier_str . ' = ' : $norm->norm .' * '. $item->quantity . ' = '*/, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                    $this->pdf->MultiCell($this->width[++$column], $this->height, $item['sort'] == 0 ? round($item['print_value'] * $item['multiplier'],3) : $item['print_value']/*$norm->sort == 0 ? round($norm->norm * $norm->quantity * $item->quantity * $norm->pred_quantity_node * $multiplier,3) : round($norm->norm * $item->quantity,3)*/, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

                }
            }

            $this->pdf->Ln();
        }

        // Выводим PDF в браузер
        $this->pdf->Output('write_off_delivery_note_'.$this->order->name.'.pdf', 'I');
    }

    private function setNewList($header1, $header2,$width,$height=180)
    {
        if($this->pdf->getY() >= $height) {
            $this->pdf->Ln();
            $this->pdf->Cell(0, 10, 'ЛИСТ '.$this->page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
            $this->pdf = PDFService::getHeaderPdf($this->pdf, $header1, $header2, $width);
            $this->page++;
        }
    }

    private function getRecords()
    {
        $records = DeliveryNote
            ::whereIn('id',$this->ids)
            ->orderBy('document_number')
            ->with('designationMaterial.material','designationMaterial.designation','orderName')
            ->get();

        return $this->getMaterials($records);
    }

    private function getMaterials($records)
    {
        return $this->materialService->material($records,$this->type_report,$this->sender_department_id,'material_id');

    }

    private function getMaterialPdf($materials)
    {
        $this->pdf->SetFont('dejavusans', 'B', 14);

        $this->pdf->Cell(0, 10, "Разом по матеріалам",0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку

        $this->pdf->SetFont('dejavusans', '', 10);

        foreach ($materials as $item) {

            $this->setNewList($this->header1,$this->header2,$this->width);

            $this->pdf->MultiCell($this->width[0], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[1], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[2], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[3], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[4], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[5], $this->height, '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[6], $this->height, $item['code_1c'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[7], $this->height, $item['material'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[8], $this->height,$item['unit'], 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[9], $this->height, $item['sort'] == 0 ? $item['print_number'] . $item['multiplier_str'] . ' = ' :  $item['print_number']/*$item['sort'] == 0 ? $item['quantity_norm_quantity_detail']. $multiplier_str .' = ' : $item['quantity_norm_quantity_detail']*/, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[10], $this->height, $item['sort'] == 0 ? round($item['print_value'] * $item['multiplier'],3) : ''/*round($item['quantity_norm_quantity_detail'] * $multiplier,3) : ''*/, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->Ln();
        }

        $this->pdf->Output('write_off_materials_'.$this->order->name.'.pdf', 'I');
    }
}
