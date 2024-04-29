<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\HelpService\PDFService;
use App\Services\Statements\ApplicationStatementPrintService;
use Illuminate\Http\Request;

class ApplicationStatement extends Controller
{
    public function applicationStatement($filter,$order_number)
    {
        $data =  ApplicationStatementPrintService::queryAppStatement($filter,$order_number);

        $width = array(25,50,100,40,20,20,30);

        $header1 = [ 'Замовлення',
            "Познач. деталі ",
            'Найменування дсе',
            'Познач.',
            'кіл-ть',
            'кіл-ть',
            'техмаршрут'
        ];
        $header2 = [ '',
            "складал. од.(що)",
            '',
            'складал. од.(куда)',
            'на вузол',
            'на виріб',
            ''
        ];
        $pdf = PDFService::getPdf($header1,$header2,$width,'');
        $page = 2;

        $previous_chto = '';
        // Добавление данных таблицы
        foreach ($data as $item) {

            if($pdf->getY() >= 185) {
                $pdf->Cell(0, 5, 'ЛИСТ '.$page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                $pdf = PDFService::getHeaderPdf($pdf, $header1, $header2, $width);
                $page++;
            }
            if($previous_chto == $item->chto){
                $chto = '';
                $chto_name = '';
            }else{
                $chto = $item->chto;
                $chto_name = $item->chto_name;
                $previous_chto = $item->chto;
            }

            $pdf->Cell($width[0], 10, $item->zakaz);
            $pdf->Cell($width[1], 10, $chto);
            $pdf->Cell($width[2], 10, $chto_name);
            $pdf->Cell($width[3], 10, $item->kuda);
            $pdf->Cell($width[4], 10, $item->kols);
            $pdf->Cell($width[5], 10, $item->kolzak);
            $pdf->Cell($width[6], 10, $item->route);
            $pdf->Ln();
        }
        $pdf->AddPage();

        $pdf->setY(100);
        $pdf->Cell(0, 5, 'АВТОМАТИЗОВАНА СИСТЕМА УПРАВЛІННЯ',0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
        $pdf->Cell(0, 5, '',0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку

        $pdf->Cell(0, 5, 'ПІДПРИЄМСТВОМ',0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
        $pdf->Cell(0, 5, '',0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку

        $pdf->Cell(0, 5, 'ПІДСИСТЕМА ТЕХНІЧНОЇ ПІДГОТОВКИ ВИРОБНИЦТВА',0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
        $pdf->Cell(0, 5, '',0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку

        $pdf->Cell(0, 5, 'ВІДОМІСТЬ ЗАСТОСУВАННЯ',0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку


        // Выводим PDF в браузер
        $pdf->Output('example.pdf', 'I');

    }
}
