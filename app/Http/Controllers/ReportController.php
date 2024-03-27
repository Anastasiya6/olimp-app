<?php

namespace App\Http\Controllers;

use App\Models\ReportApplicationStatement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use TCPDF;

class ReportController extends Controller
{
    public function specificationNormMaterial()
    {
        $items = ReportApplicationStatement::has('designationMaterial.material')->with('designation')->get();

        $data = $items->map(function ($item) {
            return [
                'id' => $item->designationMaterial->material->id,
                'name' => $item->designationMaterial->material->name,
                'unit' => $item->designationMaterial->material->unit->unit,
                'norm' => $item->designationMaterial->norm,
                'department' => substr($item->designation->route,0,2),
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
        $pdf = new TCPDF('L',  'mm', PDF_PAGE_FORMAT, 'A4', 'UTF-8', false);

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

    public function DetailspecificationNormMaterial()
    {
        $items = ReportApplicationStatement::/*whereHas('designation', function ($query) {
            $query->where('department_id', '08');
        })
            ->*/has('designationMaterial.material')
            ->with('designation')
            ->get();

        $data = $items->sortBy('id')->map(function ($item) {
            return [
                'id' => $item->designationMaterial->material->id,
                'material_name' => $item->designationMaterial->material->name,
                'detail_name' => $item->designation->designation,
                'quantity_total' => $item->quantity_total,
                'unit' => $item->designationMaterial->material->unit->unit,
                'norm' => $item->designationMaterial->norm,
            ];
        });

        $previousMaterial = null;
        $fileContent = '';
        foreach ($data as $row) {
            if ($row['material_name'] !== $previousMaterial) {
                $fileContent .= $row['material_name'] . PHP_EOL;
                $previousMaterial = $row['material_name'];
            }

            $fileContent .= str_repeat(' ', strlen($row['material_name'])) . sprintf("%-80s%-10s%-10s%-10s", $row['detail_name'], $row['quantity_total'], $row['norm'], $row['quantity_total']*$row['norm']) . PHP_EOL;

        }
$pdf = new TCPDF('L', 'mm', PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Устанавливаем свойства PDF
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Report');
$pdf->SetSubject('Report');
$pdf->SetKeywords('Keywords');

// Добавляем новую страницу
$pdf->AddPage();

// Устанавливаем шрифт и размер текста
$pdf->SetFont('dejavusans', '', 10);

// Заголовок таблицы
$header = ['Material Name', 'Detail Name', 'Total Quantity', 'Unit', 'Norm'];

// Ширина столбцов
$columnWidths = [40, 50, 30, 20, 20];

// Вывод заголовка таблицы
foreach ($header as $key => $column) {
    $pdf->Cell($columnWidths[$key], 10, $column, 1, 0, 'C');
}
$pdf->Ln();

// Вывод данных таблицы
foreach ($data as $row) {
    foreach ($row as $key => $value) {
        $pdf->Cell($columnWidths[$key], 10, $value, 1, 0, 'C');
    }
    $pdf->Ln();
}

// Выводим PDF в браузер
$pdf->Output('example.pdf', 'I');
    }
}
