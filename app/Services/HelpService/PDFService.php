<?php

namespace App\Services\HelpService;

use TCPDF;

class PDFService
{
    public static function getPdf($header1 = array(),$header2 = array(),$width = array(),$title = 'Report',$string = '',$orientation = 'L')
    {

        $pdf = new TCPDF($orientation, 'mm', PDF_PAGE_FORMAT, 'A4', 'UTF-8', false);

        // Устанавливаем свойства PDF
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        if($title!=''){
            $pdf->SetTitle($title);
        }
        $pdf->SetSubject('Your Subject');
        $pdf->SetKeywords('Keywords');
        $pdf->AddPage();
        // Добавление заголовка перед таблицей
        $pdf->SetFont('dejavusans', '', 10);

        $header_height = 5;

        if($title!=''){
            $pdf->Cell(0, $header_height, $title, 0, 1, 'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
        }
        if($string!=''){
            $pdf->Cell(0, $header_height, $string, 0, 1, 'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
        }

        $pdf->Cell(0, $header_height, 'ЛИСТ '.$pdf->getPage(), 0, 1, 'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку

        // Устанавливаем стиль линии
        $pdf->SetLineStyle(array('dash' => 2, 'color' => array(0, 0, 0))); // Пунктирная линия черного цвета

        // Добавление заголовка таблицы
        $pdf->SetFont('dejavusans', '', 10);

        $pdf = self::getHeaderPdf($pdf,$header1,$header2,$width);
        // Установка шрифта для данных таблицы
        $pdf->SetFont('dejavusans', '', 10);

        return $pdf;

    }

    public static function getHeaderPdf($pdf, $header1 = array(),$header2 = array(),$width)
    {
        $lastIndex = count($header1) - 1;

        $header_height = 5;

        foreach ($header1 as $key => $value) {

            if ($key === $lastIndex) {

                $pdf->Cell($width[$key], $header_height, $value, 'T', 1, 'C'); // Переход на новую строку

            } else {

                $pdf->Cell($width[$key], $header_height, $value, 'TR', 0, 'C');
            }

        }
        foreach ($header2 as $key => $value) {

            if ($key === $lastIndex) {

                $pdf->Cell($width[$key], $header_height, $value, 'B', 1, 'C'); // Переход на новую строку

            } else {

                $pdf->Cell($width[$key], $header_height, $value, 'RB', 0, 'C');
            }

        }
        return $pdf;
    }
}
