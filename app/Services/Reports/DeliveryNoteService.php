<?php

namespace App\Services\Reports;
use App\Models\DeliveryNote;
use App\Models\Department;
use App\Models\Order;
use App\Models\OrderName;
use App\Models\PlanTask;
use App\Models\ReportApplicationStatement;
use App\Repositories\Interfaces\OrderNameRepositoryInterface;
use App\Services\HelpService\PDFService;
use Illuminate\Support\Facades\DB;

class DeliveryNoteService
{
    public $width = array(70,70,40,40,30,20);

    public $height = 10;

    public $max_height = 10;
    // Заголовок таблицы
    public $header1 = ['Номер деталі',
                        'Найменування деталі',
                        'Номер',
                        'Дата',
                        'Замовлення',
                        'Кіл-ть'];
    public $header2 = ['',
                        '',
                        'документа',
                        'документа',
                        '',
                        ''];
    public $pdf = null;

    public $page = 2;

    public $sender_department;

    public $receiver_department;

    public $sender_department_number;

    public $receiver_department_number;

    public $order_name_id;

    public $document_date;

    private $orderNameRepository;

    public function __construct(OrderNameRepositoryInterface $orderNameRepository)
    {
        $this->orderNameRepository = $orderNameRepository;
    }

    public function deliveryNote($sender_department,$receiver_department,$order_name_id, $document_date)
    {
        $this->sender_department = $sender_department;

        $this->receiver_department = $receiver_department;

        $this->order_name_id = $order_name_id;

        $this->document_date = $document_date;

        $sender_department_number = Department::where('id',$this->sender_department)->first();

        $receiver_department_number = Department::where('id',$this->receiver_department)->first();

        if(!$sender_department_number || !$receiver_department_number){
            exit;
        }

        $this->sender_department_number = $sender_department_number->number;

        $this->receiver_department_number = $receiver_department_number->number;

        $delivery_notes_items = $this->getDeliveryNotesItems();

        $this->getPdf($delivery_notes_items,$delivery_notes_items);

    }
    public function getPdf($delivery_notes_items,$report_application_items)
    {
        $order_number = $this->orderNameRepository->getByOrderFirst($this->order_name_id);

        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'ЗДАТОЧНІ',' З цеха '.$this->sender_department_number.' у цех '.$this->receiver_department_number .' ЗАМОВЛЕННЯ №'.$order_number->name);

        // Добавление данных таблицы
        foreach ($report_application_items as $item) {

            if($this->pdf->getY() >= 185) {
                $this->pdf->Cell(0, 5, 'ЛИСТ '.$this->page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                $this->pdf = PDFService::getHeaderPdf($this->pdf, $this->header1, $this->header2, $this->width);
                $this->page++;
            }

            $this->pdf->MultiCell($this->width[0], $this->height, $item->designation->designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[1], $this->height, $item->designation->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[2], $this->height, $item->document_number, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[3], $this->height, \Carbon\Carbon::parse($item->document_number)->format('d.m.Y'), 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[4], $this->height, $item->orderName->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[5], $this->height, $item->quantity, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->Ln();
        }

        // Выводим PDF в браузер
        $this->pdf->Output('delivery_note_'.$order_number.'.pdf', 'I');
    }

    private function getDeliveryNotesItems()
    {
        return DeliveryNote
            ::where('order_name_id',$this->order_name_id)
            ->where('sender_department_id',$this->sender_department)
            ->where('receiver_department_id',$this->receiver_department)
            ->where('document_date',$this->document_date)
            ->with('designation')
            ->get();
    }
}
