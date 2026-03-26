<?php

namespace App\Services\ImportMaterialStock;
use App\Models\ImportMaterial;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class ImportMaterialStock
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {

            // пропускаємо заголовок
            if ($index === 0) {
                continue;
            }

            ImportMaterial::create(
                [   'code' => $row[1],
                    'article' => $row[2],
                    'name'   => $row[3],
                    'type_unit_id' => 1
                ]
            );
        }
    }

}
