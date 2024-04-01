<?php

namespace App\Console\Commands;

use App\Models\ReportApplicationStatement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DetailSpecificationNormMaterial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:detail-specification-norm-material';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
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
        $filePath = 'specif.txt';

        // Записываем данные в файл
        Storage::disk('public')->put($filePath, $fileContent);

        // Возвращаем путь к файлу для скачивания или отображения
        return $filePath;

        // Записываем данные в файл
        $filePath = 'output.txt';
        file_put_contents($filePath, $fileContent);

        foreach ($data as $row) {
            if ($row['material_name'] !== $previousMaterial) {
                echo $row['material_name'] . PHP_EOL;
                $previousMaterial = $row['material_name'];
            }
            echo str_repeat(' ', strlen($row['material_name'])) . $row['detail_name'] . PHP_EOL;
        }

    }
}
