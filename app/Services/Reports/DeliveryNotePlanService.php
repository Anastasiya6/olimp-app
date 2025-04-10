<?php

namespace App\Services\Reports;
use App\Models\DeliveryNote;
use App\Models\Department;
use App\Models\Order;
use App\Models\OrderName;
use App\Models\PlanTask;
use App\Models\ReportApplicationStatement;
use App\Repositories\Interfaces\OrderNameRepositoryInterface;
use App\Services\HelpService\HelpService;
use App\Services\HelpService\PDFService;
use Illuminate\Support\Facades\DB;

class DeliveryNotePlanService
{
    public $width = array(45,63,30,30,30,30,30);

    public $height = 10;

    public $max_height = 10;
    // Заголовок таблицы
    public $header1 = ['Номер деталі',
                        'Найменування деталі',
                        'Застосовність',
                        'Загальна',
                        'К-ть по'];
    public $header2 = ['',
                        '',
                        '',
                        'к-ть',
                        'здаточним'];
    public $pdf = null;

    public $page = 2;

    public $sender_department;

    public $receiver_department;

    public $sender_department_number;

    public $receiver_department_number;

    public $order_name_id;

    private $orderNameRepository;

    public function __construct(OrderNameRepositoryInterface $orderNameRepository)
    {
        $this->orderNameRepository = $orderNameRepository;
    }

    public function deliveryNote($sender_department,$receiver_department,$order_name_id)
    {
        $this->sender_department = $sender_department;
      //  dd($sender_department,$receiver_department,$order_name_id);

        $this->receiver_department = $receiver_department;

        $this->order_name_id = $order_name_id;

        $sender_department_number = Department::where('id',$this->sender_department)->first();

        $receiver_department_number = Department::where('id',$this->receiver_department)->first();

        if(!$sender_department_number || !$receiver_department_number){
            exit;
        }

        $this->sender_department_number = $sender_department_number->number;

        $this->receiver_department_number = $receiver_department_number->number;

        $this->addInPlanTaskFromDeliveryNote();

        $delivery_notes_items = $this->getDeliveryNotesItems();

        $sortedResults = $this->getSortedItems();

        $this->getPdf($delivery_notes_items,$sortedResults);

    }

    public function getPdf($delivery_notes_items,$report_application_items)
    {
        $order_number = $this->orderNameRepository->getByOrderFirst($this->order_name_id);
        //dd($order_number);
        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'ЗДАТОЧНІ З ПЛАНОМ',' З цеха '.$this->sender_department_number.' у цех '.$this->receiver_department_number .' ЗАМОВЛЕННЯ №'.$order_number->name,'P');

        // Добавление данных таблицы
        foreach ($report_application_items as $item) {
         //   dd($item);
            if($this->pdf->getY() >= 275) {
                $this->pdf->Cell(0, 5, 'ЛИСТ '.$this->page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                $this->pdf = PDFService::getHeaderPdf($this->pdf, $this->header1, $this->header2, $this->width);
                $this->page++;
            }

            $this->pdf->MultiCell($this->width[0], $this->height, $item->designation->designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[1], $this->height, $item->designation->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[3], $this->height, $item->quantity, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[4], $this->height, $item->quantity * $order_number->quantity, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[5], $this->height, $delivery_notes_items[$item->designation_id] ?? '', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->Ln();
        }

        // Выводим PDF в браузер
        $this->pdf->Output('delivery_note_'.$order_number.'.pdf', 'I');
    }

    private function addInPlanTaskFromDeliveryNote(){

        $records = DeliveryNote
            ::select(
                'designation_id'
            )
            ->where('order_name_id', $this->order_name_id)
            ->whereNotIn('designation_id', function($query) {
                $query->select('designation_id')
                    ->from('plan_tasks')
                    ->where('order_name_id',$this->order_name_id);
            })
            ->where('sender_department_id', $this->sender_department)
            ->where('receiver_department_id', $this->receiver_department)
            ->groupBy('designation_id')
            ->with('designation')
            ->get();

        foreach($records as $detail) {
            $attributes = [
                'order_name_id' => $this->order_name_id,
                'designation_id' => $detail->designation_id,
            ];

            $values = [
                'category_code' => 0,
                'quantity' => 0,
                'quantity_total' => 0,
                'sender_department_id' => $this->sender_department,
                'receiver_department_id' => $this->receiver_department,
                'order_designationEntry' =>HelpService::getNumbers($detail->designation->designation) ,
                'order_designationEntry_letters' => HelpService::getLetters($detail->designation->designation),
                'is_report_application_statement' => 2 // зі здаточних
            ];

            PlanTask::firstOrCreate($attributes, $values);
        }
    }

    private function getDeliveryNotesItems()
    {
        return DeliveryNote
            ::select(
                'designation_id',
                DB::raw('sum(quantity) as quantity'))
            ->where('order_name_id',$this->order_name_id)
            ->where('sender_department_id',$this->sender_department)
            ->where('receiver_department_id',$this->receiver_department)
            ->groupBy('designation_id')
            ->with('designation')
            ->pluck('quantity','designation_id')
            ->toArray();
    }

    private function getSortedItems()
    {
        $results = PlanTask
            ::select(
                'designation_id as designation_id',
                'quantity')
            ->where('order_name_id',$this->order_name_id)
            ->where('order_designationEntry_letters','!=','ПИ')
            ->where('order_designationEntry_letters','!=','КР')
            ->where('sender_department_id', $this->sender_department)
            ->where('receiver_department_id', [$this->receiver_department])
            //->groupBy('designation_id')
            //->havingRaw('sender_department_id = ?', [$this->sender_department])
           // ->havingRaw('receiver_department_id = ?', [$this->receiver_department])
            ->with('designation')
            ->get();

        /*Додаю записи зі Здаточних, яких немає в плані*/
        /* $secondQuery = DeliveryNote::select(
            'designation_id as designation_id',
             DB::raw('0 as quantity'))
            ->where('order_name_id', $this->order_name_id)
            ->whereNotIn('designation_id', function($query) {
                $query->select('designation_id')
                    ->from('plan_tasks')
                    ->where('order_name_id',$this->order_name_id);
            })
            ->where('sender_department_id', $this->sender_department)
            ->where('receiver_department_id', $this->receiver_department)
            ->groupBy('designation_id')
            ->with('designation')
            ->get();*/

        //$results = $firstQuery->concat($secondQuery);

        return $results->sortBy(function($item) {
            if (isset($item->designation)) {
                return $item->designation->designation;
            }else {
                return ''; // В случае отсутствия обоих полей
            }
        });
    }
}
