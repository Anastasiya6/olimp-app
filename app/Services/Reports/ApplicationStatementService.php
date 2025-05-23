<?php

namespace App\Services\Reports;
use App\Models\DesignationMaterial;
use App\Models\GroupMaterial;
use App\Models\Material;
use App\Models\ReportApplicationStatement;
use App\Services\HelpService\PDFService;
use App\Services\Statements\ApplicationStatementPrintService;
use TCPDF;

class ApplicationStatementService
{
    public $height = 10;

    public $max_height = 10;

    public $width = array(23,43,110,45,17,17,30);

    public $header1 = [ 'Замовлення',
                        "Познач. деталі ",
                        'Найменування дсе',
                        'Познач.',
                        'кіл-ть',
                        'кіл-ть',
                        'техмаршрут'
                        ];
    public $header2 = [ '',
                        "складал. од.(що)",
                        '',
                        'складал. од.(куда)',
                        'на вузол',
                        'на виріб',
                        ''
                        ];

    public $pdf = null;

    public $page = 2;

    public function applicationStatement($filter,$order_name_id,$department)
    {
        $data =  ApplicationStatementPrintService::queryAppStatement($filter,$order_name_id,$department);

        $this->pdf = PDFService::getPdf($this->header1,$this->header2,$this->width,'');

        $previous_chto = '';
        // Добавление данных таблицы
        foreach ($data as $item) {

            if($this->pdf->getY() >= 185) {
                $this->pdf->Cell(0, 5, 'ЛИСТ '.$this->page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                $this->pdf = PDFService::getHeaderPdf($this->pdf, $this->header1, $this->header2, $this->width);
                $this->page++;
            }
            if($previous_chto == $item->chto){
                $chto = '';
                $chto_name = '';
            }else{
                $chto = $item->chto;
                $chto_name = $item->chto_name.' '.$item->gost;
                $previous_chto = $item->chto;
            }
            $this->pdf->MultiCell($this->width[0], $this->height, $item->zakaz, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[1], $this->height, $chto, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[2], $this->height, $chto_name, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[3], $this->height, $item->kuda, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[4], $this->height, $item->kols, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[5], $this->height, $item->kolzak, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->MultiCell($this->width[6], $this->height, $item->route==''? '   '.$item->tm : $item->tm, 0, 'L', 0, 0, '', '', true, 0, false, true, $this->max_height, 'T');
            $this->pdf->Ln();
        }
        $this->pdf->AddPage();

        $this->pdf->setY(100);
        $this->pdf->Cell(0, 5, 'АВТОМАТИЗОВАНА СИСТЕМА УПРАВЛІННЯ',0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
        $this->pdf->Cell(0, 5, '',0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку

        $this->pdf->Cell(0, 5, 'ПІДПРИЄМСТВОМ',0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
        $this->pdf->Cell(0, 5, '',0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку

        $this->pdf->Cell(0, 5, 'ПІДСИСТЕМА ТЕХНІЧНОЇ ПІДГОТОВКИ ВИРОБНИЦТВА',0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
        $this->pdf->Cell(0, 5, '',0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку

        $this->pdf->Cell(0, 5, 'ВІДОМІСТЬ ЗАСТОСУВАННЯ',0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
        $this->pdf->Cell(0, 5, '',0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку

        $this->pdf->Cell(0, 5, 'ЦЕХ '.$department,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку

        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->Ln();
        $this->pdf->Ln();

        $this->pdf->Cell(0, 5, '                                            ДАТА '.\Carbon\Carbon::now()->format('d.m.Y').'                                                                                        '.'ГОЛОВНИЙ ТЕХНОЛОГ',0,1,'L'); // 'L' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку

        // Выводим PDF в браузер
        $this->pdf->Output('example.pdf', 'I');

    }
}
