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
    public $width = array(70,100,40,40,10,10);

    public $height = 10;

    public $max_height = 10;
    // Заголовок таблицы
    public $header1 = ['Номер деталі',
                        'Найменування деталі',
                        'К-ть по',
                        'К-ть по'];
    public $header2 = ['',
                        '',
                        'плану',
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

        $this->receiver_department = $receiver_department;

        $this->order_name_id = $order_name_id;

        $sender_department_number = Department::where('id',$this->sender_department)->first();

        $receiver_department_number = Department::where('id',$this->receiver_department)->first();

        if(!$sender_department_number || !$receiver_department_number){
            exit;
        }

        $this->sender_department_number = $sender_department_number->number;

        $this->receiver_department_number = $receiver_department_number->number;

        $delivery_notes_items = $this->getDeliveryNotesItems();

        $sortedResults = $this->getSortedItems();

        $this->getPdf($delivery_notes_items,$sortedResults);

    }
    public function getPdf($delivery_notes_items,$report_application_items)
    {
        $order_number = $this->orderNameRepository->getByOrderFirst($this->order_name_id);


        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'ЗДАТОЧНІ З ВІДОМІСТЮ ЗАСТОСУВАННЯ',' З цеха '.$this->sender_department_number.' у цех '.$this->receiver_department_number .' ЗАМОВЛЕННЯ №'.$order_number->name);

        // Добавление данных таблицы
        foreach ($report_application_items as $item) {
            if($this->pdf->getY() >= 185) {
                $this->pdf->Cell(0, 5, 'ЛИСТ '.$this->page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                $this->pdf = PDFService::getHeaderPdf($this->pdf, $this->header1, $this->header2, $this->width);
                $this->page++;
            }

            $this->pdf->MultiCell($this->width[0], $this->height, $item->designationEntry->designation??$item->designation->designation, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[1], $this->height, $item->designationEntry->name??$item->designation->name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[2], $this->height, $item->quantity_total, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->MultiCell($this->width[3], $this->height, isset($delivery_notes_items[$item->designation_entry_id])?$delivery_notes_items[$item->designation_entry_id]:'', 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');

            $this->pdf->Ln();
        }

        // Выводим PDF в браузер
        $this->pdf->Output('delivery_note_'.$order_number.'.pdf', 'I');
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
        $firstQuery = PlanTask
            ::select(
                'designation_entry_id as designation_id',
                'designation_entry_id',
                DB::raw('sum(quantity_total) as quantity_total'),
                DB::raw('"" as quantity'))
            ->where('order_name_id',$this->order_name_id)
            ->where('order_designationEntry_letters','!=','ПИ')
            ->where('order_designationEntry_letters','!=','КР')
            ->groupBy('designation_entry_id')
            ->havingRaw('MIN(LEFT(tm, 2)) = ?', [$this->sender_department_number])
            ->havingRaw('MIN(SUBSTR(tm, -2)) = ?', [$this->receiver_department_number])
            ->with('designationEntry')
            ->get();

        $secondQuery = DeliveryNote::select(
            'designation_id as designation_id' ,
            'designation_id as designation_entry_id' ,
            DB::raw('"" as quantity_total'),
            DB::raw('sum(quantity) as quantity')
        )
            ->where('order_name_id', $this->order_name_id)
            ->whereNotIn('designation_id', function($query) {
                $query->select('designation_entry_id')
                    ->from('plan_tasks');
            })
            ->where('sender_department_id', $this->sender_department)
            ->where('receiver_department_id', $this->receiver_department)
            ->groupBy('designation_id')
            ->with('designation')
            ->get();
        //dd($secondQuery);
        $results = $firstQuery->concat($secondQuery);

        return $results->sortBy(function($item) {
            if (isset($item->designationEntry)) {
                return $item->designationEntry->designation;
            } elseif (isset($item->designation)) {
                return $item->designation->designation;
            } else {
                return ''; // В случае отсутствия обоих полей
            }
        });
    }
}
