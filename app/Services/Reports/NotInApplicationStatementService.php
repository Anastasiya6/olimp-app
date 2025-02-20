<?php

namespace App\Services\Reports;

use App\Models\DeliveryNote;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;
use App\Repositories\Interfaces\OrderNameRepositoryInterface;
use App\Services\HelpService\PDFService;
use Illuminate\Support\Facades\Log;

class NotInApplicationStatementService
{

    public $height = 10;

    public $max_height = 10;

    public $width = array(60,110,30);

    public $header1 = [ 'Номер',
                        "Назва",
                        "Замовлення"
                    ];
    public $header2 = [ '',
                        "",
                        ""
                        ];
    public $page = 2;

    public $pdf = null;

    private $orderNameRepository;

    private $departmentRepository;

    public $sender_department_number;

    public $order_name_id;

    public function __construct(DepartmentRepositoryInterface $departmentRepository, OrderNameRepositoryInterface $orderNameRepository)
    {
        $this->orderNameRepository = $orderNameRepository;

        $this->departmentRepository = $departmentRepository;
    }

    public function notInApplicationStatement($sender_department, $order_name_id)
    {
        $this->order_name_id = $this->orderNameRepository->getByOrderFirst($order_name_id)?->id;

        $this->sender_department_number = $this->departmentRepository->getByDepartmentIdFirst($sender_department)?->id;

        if(!$this->order_name_id || !$this->sender_department_number){
            exit;
        }

        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'Відсутні у відомості застосування','','P');

        $records = DeliveryNote::select('designation_id', 'designations.designation','designations.name','order_names.name as order_name')
            ->distinct() // Унікальні записи
            ->join('order_names', 'order_names.id', '=', 'delivery_notes.order_name_id') // JOIN з order_names
            ->join('designations', 'designations.id', '=', 'delivery_notes.designation_id')
            ->where('order_names.id',$this->order_name_id)
            ->where('order_name_id',$this->order_name_id)
            ->where('sender_department_id', $this->sender_department_number)
            ->whereNotIn('designation_id', function ($query) {
                $query->select('designation_entry_id')
                    ->from('report_application_statements')
                    ->where('order_name_id',$this->order_name_id);// Підзапит
            })
            ->orderBy('designations.designation')
            ->get();

        foreach ($records as $record) {

            $this->pdf->Cell($this->width[0], $this->height, $record->designation);
            $this->pdf->Cell($this->width[1], $this->height, $record->name);
            $this->pdf->Cell($this->width[2], $this->height, $record->order_name);

            $this->pdf->Ln();

        }

        $pdf_path = storage_path('app/public/pi0.pdf');
        $this->pdf->Output($pdf_path, 'I');

    }


    public function newList()
    {
        if ($this->pdf->getY() >= 275) {
            $this->pdf->Cell(0, 5, 'ЛИСТ ' . $this->page, 0, 1, 'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
            $this->pdf = PDFService::getHeaderPdf($this->pdf, $this->header1, $this->header2, $this->width);
            $this->page++;
        }
    }
}
