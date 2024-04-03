<?php

namespace App\Http\Controllers;

use App\Models\ReportApplicationStatement;
use App\Services\HelpService\PDFService;
use App\Services\Statements\ApplicationStatementPrintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use TCPDF;

class ReportController extends Controller
{
    public function specificationNormMaterial()
    {
        $items = ReportApplicationStatement::has('designationMaterial.material')->with('designationEntry')->get();

        $data = $items->map(function ($item) {
            return [
                'id' => $item->designationMaterial->material->id,
                'name' => $item->designationMaterial->material->name,
                'unit' => $item->designationMaterial->material->unit->unit,
                'norm' => $item->designationMaterial->norm,
                'department' => substr($item->designationEntry->route, 0, 2),
            ];
        });

        $groupedData = $data->groupBy('id')->map(function ($items) {
            return [
                'name' => $items->first()['name'], // Берем название материала из первого элемента группы
                'unit' => $items->first()['unit'], // Берем единицу измерения из первого элемента группы
                'department' => $items->first()['department'], // Берем цех из первого элемента группы
                'norm' => $items->sum('norm'), // Суммируем количество по всем элементам группы
            ];
        });

        // Создаем новый экземпляр TCPDF
        $pdf = new TCPDF('L', 'mm', PDF_PAGE_FORMAT, 'A4', 'UTF-8', false);

        // Устанавливаем свойства PDF
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Специфицированные нормы расхода материалов на изделие');
        $pdf->SetSubject('Your Subject');
        $pdf->SetKeywords('Keywords');

        $pdf->AddPage();
        $header1 = 'Специфицированные нормы расхода материалов на изделие';

        // Добавление заголовка перед таблицей
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(180, 10, $header1, 0, 1, 'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
        $pdf->Ln(); // Добавляем пустую строку после заголовка

        // Заголовок таблицы
        $header = ['Наименование материала', 'Ед.измер', 'Норма расхода изделие', 'Цех'];

        // Устанавливаем стиль линии
        $pdf->SetLineStyle(array('dash' => 2, 'color' => array(0, 0, 0))); // Пунктирная линия черного цвета

        // Добавление заголовка таблицы
        $pdf->SetFont('dejavusans', 'B', 10);

        $pdf->Cell(120, 10, $header[0], 'TRB', 0, 'C');
        $pdf->Cell(30, 10, $header[1], 'TRB', 0, 'C');
        $pdf->Cell(70, 10, $header[2], 'TRB', 0, 'C');
        $pdf->Cell(10, 10, $header[3], 'TB', 1, 'C'); // Переход на новую строку

        // Установка шрифта для данных таблицы
        $pdf->SetFont('dejavusans', '', 10);

        // Добавление данных таблицы
        foreach ($groupedData as $item) {
            $pdf->Cell(120, 10, $item['name']);
            $pdf->Cell(30, 10, $item['unit']);
            $pdf->Cell(70, 10, $item['norm']);
            $pdf->Cell(10, 10, $item['department']);
            $pdf->Ln();
        }

        // Выводим PDF в браузер
        $pdf->Output('example.pdf', 'I');
    }

    public function detailSpecificationNormMaterial()
    {
        $data = ApplicationStatementPrintService::detailspecificationNormMaterial();

        $width = array(80,40,30,20,50,50);
        $header1 = ['Найменування матеріалу',
                    'Найменування DSE',
                    'Застосовність',
                    'Од.виміру',
                    'Норма витрат',
                    'Норма на застосування'];

        $header2 = [ '',
            '',
            '',
            '',
            'на един',
            ''];

        $pdf = PDFService::getPdf($header1,$header2,$width,'ПОДЕТАЛЬНО-СПЕЦИФІКОВАННІ НОРМИ ВИТРАТ МАТЕРІАЛІВ НА ВИРІБ','ЦЕХ 25 ЗАКАЗ 28070');
        $page = 2;

        // Устанавливаем стиль линии
        $pdf->SetLineStyle(array('dash' => 2, 'color' => array(0, 0, 0))); // Пунктирная линия черного цвета

        // Устанавливаем шрифт и размер текста
        $pdf->SetFont('dejavusans', '', 10);
        $sum_norm = 0;

        $first = 1;
        // Переменная для хранения предыдущего материала
        $previousMaterial = null;
        foreach ($data as $row) {

            if($pdf->getY() >= 185) {
                $pdf->Cell(0, 5, 'ЛИСТ '.$page,0,1,'C'); // 'C' - выравнивание по центру, '0' - без рамки, '1' - переход на новую строку
                $pdf = PDFService::getHeaderPdf($pdf, $header1, $header2, $width);
                $page++;
             }
            // Если текущий материал отличается от предыдущего, добавляем его в PDF
            if ($row['id'] !== $previousMaterial) {

                if($first > 1){
                    $pdf->Cell(270, 10, 'Разом по матер. '.$sum_norm,0,1,'R');
                    $pdf->Ln();
                }
                $first++;
                $sum_norm = 0;

                if($pdf->getY() >= 185) {
                    PDFService::getList($page,$pdf, $header1, $header2, $width);
                    $page++;
                }
                // Добавляем название материала
                $pdf->Cell(100, 10, $row['material_name']);
                $pdf->Ln();
                if($pdf->getY() >= 185) {
                    PDFService::getList($page,$pdf, $header1, $header2, $width);
                    $page++;
                }

                // Сбрасываем предыдущий материал
                $previousMaterial = $row['id'];
            }
                // Если текущий материал такой же, как предыдущий, то добавляем только детали без названия материала
                $pdf->Cell($width[0], 10, '');
                $pdf->Cell($width[1], 10, $row['detail_name']);
                $pdf->Cell($width[2], 10, $row['quantity_total']);
                $pdf->Cell($width[3], 10, $row['unit']);
                $pdf->Cell($width[4], 10, $row['norm']);
                $pdf->Cell($width[5], 10, $row['norm']*$row['quantity_total']);
                $pdf->Ln();

                $sum_norm = $sum_norm + $row['norm']*$row['quantity_total'];

        }
        // Выводим PDF в браузер
        $pdf->Output('example.pdf', 'I');
    }
}
